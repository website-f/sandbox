<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletApiController extends Controller
{
    public function balance($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        return response()->json([
            'success' => true,
            'balance' => $wallet->balance,
            'currency' => 'MYR',
        ]);
    }

    public function debit(Request $request, $userId)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        try {
            DB::beginTransaction();
            $description = $validated['description'] ?? 'RizqMall order payment';
            if (!empty($validated['reference'])) {
                $description .= ' (' . $validated['reference'] . ')';
            }
            $wallet->debit((int) $validated['amount'], $description);
            DB::commit();

            $transaction = $wallet->transactions()->latest()->first();

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction?->id,
                'balance' => $wallet->fresh()->balance,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function credit(Request $request, $userId)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $description = $validated['description'] ?? 'RizqMall wallet credit';
        if (!empty($validated['reference'])) {
            $description .= ' (' . $validated['reference'] . ')';
        }

        $wallet->credit((int) $validated['amount'], $description);

        $transaction = $wallet->transactions()->latest()->first();

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction?->id,
            'balance' => $wallet->fresh()->balance,
        ]);
    }
}
