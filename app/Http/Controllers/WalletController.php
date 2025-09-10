<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ensure wallet exists
        if (!$user->wallet) {
            $user->wallet()->create([
                'balance' => 0,
            ]);
        }

        // Load latest 20 transactions
        $user->load(['wallet.transactions' => function ($q) {
            $q->latest()->take(20);
        }]);

        // ---- Monthly chart data (already added before) ----
        $monthlyData = $user->wallet
            ->transactions()
            ->selectRaw('MONTH(created_at) as month, SUM(CASE WHEN type="credit" THEN amount ELSE -amount END) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = (int) ($monthlyData[$m] ?? 0);
        }

        // ---- NEW: Pie chart data (credits vs debits) ----
        $incoming = $user->wallet->transactions()->where('type', 'credit')->sum('amount');
        $outgoing = $user->wallet->transactions()->where('type', 'debit')->sum('amount');

        return view('wallet.index', [
            'user' => $user,
            'chartData' => $chartData,
            'pieData' => [
                'incoming' => $incoming,
                'outgoing' => $outgoing,
            ]
        ]);
    }
}
