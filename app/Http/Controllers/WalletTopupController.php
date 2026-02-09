<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletTopupController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to topup your wallet.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amountCents = (int) round(((float) $validated['amount']) * 100);

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $topup = WalletTopup::create([
            'user_id' => $user->id,
            'amount' => $amountCents,
            'provider' => 'toyyibpay',
            'status' => 'pending',
        ]);

        $cfg = config('services.toyyibpay.rizqmall');
        if (!$cfg || empty($cfg['secret']) || empty($cfg['category'])) {
            return redirect()->back()->with('error', 'Payment gateway is not configured.');
        }

        $billName = 'Wallet Topup';
        $billDescription = 'Wallet topup for ' . $user->name;

        $response = Http::asForm()->post("{$cfg['url']}/index.php/api/createBill", [
            'userSecretKey' => $cfg['secret'],
            'categoryCode' => $cfg['category'],
            'billName' => $billName,
            'billDescription' => $billDescription,
            'billPriceSetting' => 1,
            'billAmount' => $amountCents,
            'billReturnUrl' => route('wallet.topup.return'),
            'billCallbackUrl' => route('wallet.topup.callback'),
            'billExternalReferenceNo' => 'WALLET-' . $topup->id,
            'billTo' => $user->name,
            'billEmail' => $user->email,
            'billPhone' => preg_replace('/\D/', '', $user->profile->phone ?? '0123456789'),
            'billPayorInfo' => 1,
        ]);

        $data = $response->json()[0] ?? null;
        if (!$data || empty($data['BillCode'])) {
            $topup->update([
                'status' => 'failed',
                'payload' => $response->json(),
            ]);
            return redirect()->back()->with('error', 'Payment provider error. Please try again.');
        }

        $topup->update([
            'bill_code' => $data['BillCode'],
        ]);

        return redirect("{$cfg['url']}/{$data['BillCode']}");
    }

    public function callback(Request $request)
    {
        Log::info('Wallet topup callback payload', $request->all());

        $billCode = $request->billcode ?? $request->BillCode ?? $request->refno;
        $status = $request->status ?? $request->status_id ?? '0';
        $paidAt = $request->paid_at ?? now();

        $topup = WalletTopup::where('bill_code', $billCode)->first();
        if (!$topup) {
            return response('NOT FOUND', 404);
        }

        $wasPaid = $topup->status === 'paid';

        $topup->update([
            'status' => match ((string) $status) {
                '1' => 'paid',
                '2' => 'pending',
                '3' => 'failed',
                default => 'failed',
            },
            'paid_at' => ((string) $status) === '1' ? $paidAt : null,
            'payload' => $request->all(),
        ]);

        if ((string) $status === '1' && !$wasPaid) {
            $wallet = Wallet::firstOrCreate(['user_id' => $topup->user_id], ['balance' => 0]);
            $wallet->credit($topup->amount, 'Wallet topup via ToyyibPay');
        }

        return response('OK');
    }

    public function return(Request $request)
    {
        $billCode = $request->billcode ?? $request->refno;
        $statusId = $request->status_id ?? $request->status;

        if ($billCode && (string) $statusId === '1') {
            $topup = WalletTopup::where('bill_code', $billCode)->first();
            if ($topup && $topup->status !== 'paid') {
                $topup->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payload' => array_merge($topup->payload ?? [], ['return_data' => $request->all()]),
                ]);

                $wallet = Wallet::firstOrCreate(['user_id' => $topup->user_id], ['balance' => 0]);
                $wallet->credit($topup->amount, 'Wallet topup via ToyyibPay');
            }
        }

        return redirect()->route('wallet.users.index')->with('success', 'Wallet topup processed. Please check your balance.');
    }
}
