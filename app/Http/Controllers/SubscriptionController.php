<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Wallet;
use App\Models\Account;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request, $plan)
{
    $user = $request->user();

    // pricing
    $price = match ($plan) {
        'sandbox' => 30000,   // RM300 one-time
        'rizqmall' => 2000,   // RM20 yearly
        default => throw new \Exception("Invalid plan"),
    };


    // select ToyyibPay config based on plan
    $cfg = config("services.toyyibpay.$plan");

    // create subscription
    $subscription = Subscription::create([
        'user_id' => $user->id,
        'plan' => $plan,
        'amount' => $price,
        'status' => 'pending',
        'provider' => 'toyyibpay',
    ]);

    // ToyyibPay API
    $billName = ucfirst($plan) . " Subscription";
    $billDescription = "Payment for {$billName}";
    $billAmount = $price / 100; // ToyyibPay uses RM not cents
    $phone = preg_replace('/\D/', '', $user->profile->phone ?? '0123456789');

    $response = Http::asForm()->post("{$cfg['url']}/index.php/api/createBill", [
    'userSecretKey' => $cfg['secret'],
    'categoryCode' => $cfg['category'],
    'billName' => $billName,
    'billDescription' => $billDescription,
    'billPriceSetting' => 1,
    'billAmount' => $price,
    'billReturnUrl' => route('payment.return'),
    'billCallbackUrl' => route('payment.callback'),
    'billExternalReferenceNo' => $subscription->id,
    'billTo' => $user->name,
    'billEmail' => $user->email,
    'billPhone' => $phone, 
    'billPayorInfo' => 1,
]);

$data = $response->json()[0] ?? null;

if (!$data || empty($data['BillCode'])) {
    \Log::error('ToyyibPay failed response', ['resp' => $response->body()]);
    return back()->with('error', $response->body());
}

// save bill code
$subscription->update(['provider_ref' => $data['BillCode']]);

Payment::create([
    'subscription_id' => $subscription->id,
    'bill_code' => $data['BillCode'],
    'amount' => $price,
    'status' => 'pending',
]);

// redirect correctly (dev/prod based on config)
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
    $subscription->update([
        'status' => 'paid',
        'starts_at' => now(),
        'ends_at' => $subscription->plan === 'rizqmall'
            ? now()->addYear()
            : null,
    ]);

    $account = Account::firstOrCreate([
        'user_id' => $subscription->user_id,
        'type' => $subscription->plan,
    ]);

    $account->update([
        'active' => true,
        'expires_at' => $subscription->plan === 'rizqmall'
            ? $subscription->ends_at
            : null,
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
}

