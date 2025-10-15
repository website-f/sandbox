<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserApiController extends Controller
{
    /**
     * Get user details for RizqMall
     * GET /api/users/{id}
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar ? url($user->avatar) : null,
                'account_type' => $user->account_type, // 'vendor' or 'customer'
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'subscription' => [
                    'status' => $user->subscription->status ?? 'inactive',
                    'plan_name' => $user->subscription->plan->name ?? null,
                    'expires_at' => $user->subscription->expires_at ?? null,
                    'is_active' => $user->subscription && $user->subscription->isActive(),
                ],
            ],
        ]);
    }

    /**
     * Get user profile with additional details
     * GET /api/users/{id}/profile
     */
    public function profile($id)
    {
        $user = User::with('subscription.plan')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar ? url($user->avatar) : null,
                'account_type' => $user->account_type,
                'bio' => $user->bio,
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'postal_code' => $user->postal_code,
                'country' => $user->country,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'subscription' => $user->subscription ? [
                    'id' => $user->subscription->id,
                    'status' => $user->subscription->status,
                    'plan' => [
                        'name' => $user->subscription->plan->name,
                        'features' => $user->subscription->plan->features,
                    ],
                    'started_at' => $user->subscription->started_at,
                    'expires_at' => $user->subscription->expires_at,
                    'is_active' => $user->subscription->isActive(),
                    'days_remaining' => $user->subscription->daysRemaining(),
                ] : null,
            ],
        ]);
    }

    /**
     * Validate access token
     * POST /api/auth/validate
     */
    public function validateToken(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided',
            ], 401);
        }

        // Validate the token using Sanctum
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$tokenModel) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        $user = $tokenModel->tokenable;

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => true,
                'user_id' => $user->id,
                'email' => $user->email,
                'expires_at' => $tokenModel->expires_at,
            ],
        ]);
    }
}

// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionApiController extends Controller
{
    /**
     * Verify subscription status
     * GET /api/subscriptions/verify/{userId}
     */
    public function verify($userId)
    {
        $user = User::with('subscription')->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $subscription = $user->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'inactive',
                    'expires_at' => null,
                    'is_active' => false,
                    'message' => 'No active subscription',
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $subscription->status,
                'plan_name' => $subscription->plan->name ?? null,
                'started_at' => $subscription->started_at,
                'expires_at' => $subscription->expires_at,
                'is_active' => $subscription->isActive(),
                'days_remaining' => $subscription->daysRemaining(),
                'auto_renew' => $subscription->auto_renew ?? false,
            ],
        ]);
    }

    /**
     * Get detailed subscription status
     * GET /api/subscriptions/{userId}/status
     */
    public function status($userId)
    {
        $user = User::with('subscription.plan')->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $subscription = $user->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'data' => [
                    'has_subscription' => false,
                    'status' => 'none',
                    'message' => 'User has no subscription',
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'has_subscription' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'plan' => [
                    'id' => $subscription->plan->id,
                    'name' => $subscription->plan->name,
                    'price' => $subscription->plan->price,
                    'billing_cycle' => $subscription->plan->billing_cycle,
                    'features' => $subscription->plan->features,
                ],
                'started_at' => $subscription->started_at,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
                'expires_at' => $subscription->expires_at,
                'cancelled_at' => $subscription->cancelled_at,
                'is_active' => $subscription->isActive(),
                'is_cancelled' => $subscription->isCancelled(),
                'is_expired' => $subscription->isExpired(),
                'days_remaining' => $subscription->daysRemaining(),
                'auto_renew' => $subscription->auto_renew ?? false,
                'payment_method' => $subscription->payment_method,
            ],
        ]);
    }
}