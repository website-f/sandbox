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
        $wallet = $user->wallet()->create([
            'balance' => 0,
        ]);
    }

    $user->load(['wallet.transactions' => function ($q) {
        $q->latest()->take(20);
    }]);

    return view('wallet.index', compact('user'));
}

}
