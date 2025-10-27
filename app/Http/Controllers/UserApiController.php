<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


class UserApiController extends Controller
{
    /**
     * Get user details for RizqMall
     * GET /api/users/{id}
     */
    public function show($id)
    {
        $user = User::with(['profile', 'accounts'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Get RizqMall account
        $rizqmallAccount = $user->accounts()
            ->where('type', 'rizqmall')
            ->with('accountType')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->profile->phone ?? null,
                'avatar' => $user->profile->photo_path ? url('storage/' . $user->profile->photo_path) : null,
                'account_type' => $rizqmallAccount ? 'vendor' : 'customer', // vendor if has rizqmall account
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'subscription' => [
                    'status' => $rizqmallAccount && $rizqmallAccount->active ? 'active' : 'inactive',
                    'plan_name' => $rizqmallAccount ? $rizqmallAccount->accountType->name : null,
                    'expires_at' => $rizqmallAccount ? $rizqmallAccount->expires_at : null,
                    'is_active' => $rizqmallAccount && $rizqmallAccount->active && 
                                   (!$rizqmallAccount->expires_at || $rizqmallAccount->expires_at->isFuture()),
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
        $user = User::with(['profile', 'accounts.accountType'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $profile = $user->profile;
        $rizqmallAccount = $user->accounts()->where('type', 'rizqmall')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $profile->phone ?? null,
                'avatar' => $profile->photo_path ? url('storage/' . $profile->photo_path) : null,
                'account_type' => $rizqmallAccount ? 'vendor' : 'customer',
                'bio' => null,
                'address' => $profile->home_address ?? null,
                'city' => $profile->city ?? null,
                'state' => $profile->state ?? null,
                'postal_code' => null,
                'country' => $profile->country ?? 'Malaysia',
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'subscription' => $rizqmallAccount ? [
                    'id' => $rizqmallAccount->id,
                    'status' => $rizqmallAccount->active ? 'active' : 'inactive',
                    'plan' => [
                        'name' => $rizqmallAccount->accountType->name,
                        'features' => [
                            'online_store' => true,
                            'unlimited_products' => true,
                            'analytics' => true,
                        ],
                    ],
                    'started_at' => $rizqmallAccount->created_at,
                    'expires_at' => $rizqmallAccount->expires_at,
                    'is_active' => $rizqmallAccount->active && 
                                   (!$rizqmallAccount->expires_at || $rizqmallAccount->expires_at->isFuture()),
                    'days_remaining' => $rizqmallAccount->expires_at ? 
                                        max(0, now()->diffInDays($rizqmallAccount->expires_at, false)) : null,
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
        // In a simple setup, we'll validate by API key in header
        // For production, implement proper token-based auth (Sanctum/Passport)
        
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('services.rizqmall.api_key');

        if ($apiKey !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401);
        }

        // Optionally validate user by email or ID
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => true,
                'user_id' => $user->id,
                'email' => $user->email,
                'expires_at' => null, // No token expiry in basic setup
            ],
        ]);
    }
}

