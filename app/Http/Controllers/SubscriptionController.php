<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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
    $phone = preg_replace('/\D/', '', $user->phone ?? '0123456789');

    $response = Http::asForm()->post("{$cfg['url']}/index.php/api/createBill", [
    'userSecretKey' => $cfg['secret'],
    'categoryCode' => $cfg['category'],
    'billName' => $billName,
    'billDescription' => $billDescription,
    'billPriceSetting' => 1,
    'billAmount' => $price, // already in cents
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
        }
         else {
            $subscription->update(['status' => 'failed']);
        }

        return response('OK');
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

