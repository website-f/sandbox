<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Wallet;
use App\Models\Account;
use App\Models\Payment;
use App\Models\Collection;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ReferralRewardService;


class SubscriptionController extends Controller
{
    public function subscribe(Request $request, $plan)
{
    $user = $request->user();

    $installments = (int) $request->input('installments', 1);

    if ($plan === 'sandbox') {
        $rizqmallAccount = Account::where('user_id', $user->id)
            ->where('type', 'rizqmall')
            ->where('active', true)
            ->first();

        if (!$rizqmallAccount) {
            return redirect()->back()->with('error', 'You must subscribe to RizqMall first before subscribing to Sandbox.');
        }
    }

    $basePrice = match ($plan) {
        'sandbox' => 30000,
        'rizqmall' => 2000,
    };

    $tax = (int) round($basePrice * 0.08);
    $fpx = 100;
    $finalPrice = $basePrice + $tax + $fpx;

    $cfg = config("services.toyyibpay.$plan");

    // installment amount
    $installmentAmount = (int) round($finalPrice / $installments);

    $subscription = Subscription::create([
        'user_id' => $user->id,
        'plan' => $plan,
        'amount' => $installmentAmount,
        'status' => 'pending',
        'provider' => 'toyyibpay',
        'installments_total' => $installments,
        'installments_paid' => 0,
        'meta' => [
            'tax' => $tax,
            'fpx' => $fpx,
            'total' => $finalPrice,
        ],
    ]);

    // create ToyyibPay bill (only for the installment amount, not full)
    $billName = ucfirst($plan) . " Subscription";
    $billDescription = "Installment " . ($subscription->installments_paid + 1) . "/" . $subscription->installments_total;

    $response = Http::asForm()->post("{$cfg['url']}/index.php/api/createBill", [
        'userSecretKey' => $cfg['secret'],
        'categoryCode' => $cfg['category'],
        'billName' => $billName,
        'billDescription' => $billDescription,
        'billPriceSetting' => 1,
        'billAmount' => $installmentAmount,
        'billReturnUrl' => route('payment.return'),
        'billCallbackUrl' => route('payment.callback'),
        'billExternalReferenceNo' => $subscription->id,
        'billTo' => $user->name,
        'billEmail' => $user->email,
        'billPhone' => preg_replace('/\D/', '', $user->profile->phone ?? '0123456789'),
        'billPayorInfo' => 1,
    ]);

    $data = $response->json()[0] ?? null;
    if (!$data || empty($data['BillCode'])) {
        return back()->with('error', 'Payment provider error.');
    }

    $subscription->update(['provider_ref' => $data['BillCode']]);

    Payment::create([
        'subscription_id' => $subscription->id,
        'bill_code' => $data['BillCode'],
        'amount' => $installmentAmount,
        'status' => 'pending',
    ]);

    return redirect("{$cfg['url']}/{$data['BillCode']}");
}



    public function paymentCallback(Request $request)
    {
        \Log::info('ToyyibPay callback payload', $request->all());

        $billCode = $request->billcode ?? $request->BillCode;
        $status = $request->status ?? '0'; // 1=success, 2=failed, 3=pending
        $paidAt = $request->paid_at ?? now();

        $payment = Payment::where('bill_code', $billCode)->first();
        if (!$payment) return response('NOT FOUND', 404);

        $payment->update([
           'status' => match($status) {
               1 => 'success',
               2 => 'pending',
               3 => 'failed',
               default => 'unknown',
           },
           'paid_at' => $status == 1 ? Carbon::parse($paidAt) : null,
           'payload' => $request->all(),
       ]);


        $subscription = $payment->subscription;

        if ($status == 1) {
            $subscription->increment('installments_paid');

            if ($subscription->installments_paid >= $subscription->installments_total) {
        // ✅ Final installment → activate
        $subscription->update([
            'status' => 'paid',
            'starts_at' => now(),
            'ends_at' => null,
        ]);

        $account = Account::firstOrCreate([
            'user_id' => $subscription->user_id,
            'type'    => $subscription->plan,
        ]);

        $account->update([
            'active' => true,
            'expires_at' => null,
        ]);

        if (!$account->serial_number) {
            $account->serial_number = Account::generateSerial($subscription->plan);
            $account->save();
        }
    } else {
        // ✅ Still in progress
        $subscription->update(['status' => 'pending']);

        // Ensure an account exists but stays inactive
        $account = Account::firstOrCreate([
            'user_id' => $subscription->user_id,
            'type'    => $subscription->plan,
        ]);

        $account->update([
            'active' => false,
            'expires_at' => null,
        ]);
    }

            \Log::info("Assigned serial number", [
                'user_id' => $subscription->user_id,
                'plan'    => $subscription->plan,
                'serial'  => $account->serial_number,
            ]);
        
            \Log::info('Distribute commission for subscription', [
                'user_id' => $subscription->user_id,
                'plan' => $subscription->plan,
                'referrer' => $subscription->user->referrer?->id,
            ]);
        
        
            // ✅ Only give commission for RizqMall
            if ($subscription->plan === 'rizqmall') {
                $this->distributeCommission($subscription);
            }

            if ($subscription->plan === 'sandbox') {
                app(\App\Services\ReferralRewardService::class)
                    ->processSandboxRewards($subscription->user);
            
                \Log::info("Referral tree rewards processed for sandbox", [
                    'user_id' => $subscription->user_id,
                    'plan'    => $subscription->plan,
                ]);
            }



        }

         else {
            $subscription->update(['status' => 'failed']);
        }

        return response('OK');
    }

    private function distributeCommission(Subscription $subscription)
{
    $user = $subscription->user;
    $amounts = [300, 100, 100]; // RM3 for direct, RM1 for 2nd and 3rd upline

    // Start from this user's referral record
    $referral = $user->referral;
    $level = 0;

    while ($referral && $level < 3) {
        $referrer = $referral->parent; // now returns User (after you fix the model)

        if (!$referrer) {
            \Log::info("No referrer found at level {$level} for user {$user->id}");
            break;
        }

        // Ensure wallet exists
        $wallet = Wallet::firstOrCreate(['user_id' => $referrer->id]);

        // Amount to credit
        $amount = $amounts[$level];

        // Credit the wallet
        $wallet->credit(
            $amount,
            "Referral commission (Level " . ($level + 1) . ") from {$user->name} subscription",
            $subscription->id
        );

        // Log the commission step
        \Log::info("Commission credited", [
            'from_user'   => $user->id,
            'to_referrer' => $referrer->id,
            'level'       => $level + 1,
            'amount'      => $amount,
        ]);

        // Move up to the next referrer
        $referral = $referrer->referral;
        $level++;
    }
}



    public function paymentReturn(Request $request)
    {
        return redirect()->route('dashboard')->with('success', 'Payment processed, please check status.');
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $subscriptions = Subscription::with('payments')->where('user_id', $user->id)->latest()->get();
        return view('subscriptions.history', compact('subscriptions'));
    }

    public function callbackTest(Request $request)
    {
        // Log incoming data for debugging
        \Log::info('Callback received:', $request->all());

        return response()->json([
            'status' => 'success',
            'data' => $request->all(),
        ]);
    }

    public function payNextInstallment(Subscription $subscription)
{
    // check ownership
    if ($subscription->user_id !== auth()->id()) {
        abort(403);
    }

    // Calculate next installment amount
    $installments = $subscription->installments_total ?? 1;
    $perInstallment = $subscription->amount; // already per installment

    // Count paid installments
    $paid = $subscription->payment()->where('status', 'success')->count();

    if ($paid >= $installments) {
        return back()->with('error', 'All installments are already paid.');
    }

    $user = $subscription->user;
    $cfg = config("services.toyyibpay.{$subscription->plan}");

    // create ToyyibPay bill (same as subscribe)
    $billName = ucfirst($subscription->plan) . " Subscription";
    $billDescription = "Installment " . ($subscription->installments_paid + 1) . "/" . $subscription->installments_total;

    $response = Http::asForm()->post("{$cfg['url']}/index.php/api/createBill", [
        'userSecretKey' => $cfg['secret'],
        'categoryCode' => $cfg['category'],
        'billName' => $billName,
        'billDescription' => $billDescription,
        'billPriceSetting' => 1,
        'billAmount' => $perInstallment,
        'billReturnUrl' => route('payment.return'),
        'billCallbackUrl' => route('payment.callback'),
        'billExternalReferenceNo' => $subscription->id,
        'billTo' => $user->name,
        'billEmail' => $user->email,
        'billPhone' => preg_replace('/\D/', '', $user->profile->phone ?? '0123456789'),
        'billPayorInfo' => 1,
    ]);

    $data = $response->json()[0] ?? null;
    if (!$data || empty($data['BillCode'])) {
        return back()->with('error', 'Payment provider error.');
    }

    $subscription->update(['provider_ref' => $data['BillCode']]);

    Payment::create([
        'subscription_id' => $subscription->id,
        'bill_code' => $data['BillCode'],
        'amount' => $perInstallment,
        'status' => 'pending',
    ]);

    return redirect("{$cfg['url']}/{$data['BillCode']}");
}


}

