<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SubscriptionApiController extends Controller
{
    /**
     * Verify subscription status
     * GET /api/subscriptions/verify/{userId}
     */
    public function verify($userId)
    {
        $user = User::with('accounts')->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $rizqmallAccount = $user->accounts()
            ->where('type', 'rizqmall')
            ->with('accountType')
            ->first();

        if (!$rizqmallAccount) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'inactive',
                    'expires_at' => null,
                    'is_active' => false,
                    'message' => 'No RizqMall subscription',
                ],
            ]);
        }

        $isActive = $rizqmallAccount->active && 
                    (!$rizqmallAccount->expires_at || $rizqmallAccount->expires_at->isFuture());

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $isActive ? 'active' : 'expired',
                'plan_name' => $rizqmallAccount->accountType->name,
                'started_at' => $rizqmallAccount->created_at,
                'expires_at' => $rizqmallAccount->expires_at,
                'is_active' => $isActive,
                'days_remaining' => $rizqmallAccount->expires_at ? 
                                    max(0, now()->diffInDays($rizqmallAccount->expires_at, false)) : null,
                'auto_renew' => false,
            ],
        ]);
    }

    /**
     * Get detailed subscription status
     * GET /api/subscriptions/{userId}/status
     */
    public function status($userId)
    {
        $user = User::with('accounts.accountType')->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $rizqmallAccount = $user->accounts()->where('type', 'rizqmall')->first();

        if (!$rizqmallAccount) {
            return response()->json([
                'success' => true,
                'data' => [
                    'has_subscription' => false,
                    'status' => 'none',
                    'message' => 'User has no RizqMall subscription',
                ],
            ]);
        }

        $isActive = $rizqmallAccount->active && 
                    (!$rizqmallAccount->expires_at || $rizqmallAccount->expires_at->isFuture());

        return response()->json([
            'success' => true,
            'data' => [
                'has_subscription' => true,
                'subscription_id' => $rizqmallAccount->id,
                'status' => $isActive ? 'active' : 'expired',
                'plan' => [
                    'id' => $rizqmallAccount->accountType->id,
                    'name' => $rizqmallAccount->accountType->name,
                    'price' => $rizqmallAccount->accountType->base_price,
                    'billing_cycle' => 'yearly',
                    'features' => [
                        'online_store' => true,
                        'unlimited_products' => true,
                        'analytics' => true,
                    ],
                ],
                'started_at' => $rizqmallAccount->created_at,
                'current_period_start' => $rizqmallAccount->created_at,
                'current_period_end' => $rizqmallAccount->expires_at,
                'expires_at' => $rizqmallAccount->expires_at,
                'cancelled_at' => null,
                'is_active' => $isActive,
                'is_cancelled' => false,
                'is_expired' => !$isActive && $rizqmallAccount->expires_at,
                'days_remaining' => $rizqmallAccount->expires_at ? 
                                    max(0, now()->diffInDays($rizqmallAccount->expires_at, false)) : null,
                'auto_renew' => false,
                'payment_method' => null,
            ],
        ]);
    }
}