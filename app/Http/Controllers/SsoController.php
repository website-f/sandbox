<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Redirect vendor to RizqMall with SSO token
     * Requires active subscription
     */
    public function redirectToRizqmall(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        // Check if user has active RizqMall account
        $rizqmallAccount = $user->accounts()
            ->where('type', 'rizqmall')
            ->where('active', 1)
            ->first();

        if (!$rizqmallAccount) {
            return redirect()->route('dashboard')
                ->with('error', 'You need an active RizqMall account to access the store. Please subscribe first.');
        }

        // Check if account is expired
        if ($rizqmallAccount->expires_at && \Carbon\Carbon::parse($rizqmallAccount->expires_at)->isPast()) {
            return redirect()->route('dashboard')
                ->with('error', 'Your RizqMall account has expired. Please renew your subscription.');
        }

        // Activate RizqMall for user if not already activated
        if (!$user->rizqmall_activated_at) {
            $user->rizqmall_activated_at = now();
            $user->rizqmall_stores_quota = 1; // First free store
            $user->save();
        }

        // Generate SSO token
        $token = $this->generateSsoToken($user, 'vendor');

        // Build redirect URL
        $rizqmallUrl = config('services.rizqmall.url', 'http://localhost:8001');
        $redirectUrl = $rizqmallUrl . '/auth/sso?token=' . $token;

        Log::info('Vendor SSO redirect', [
            'user_id' => $user->id,
            'email' => $user->email,
            'account_id' => $rizqmallAccount->id,
        ]);

        return redirect($redirectUrl);
    }

    /**
     * Redirect customer to RizqMall (no subscription required)
     */
    public function customerRedirectToRizqmall(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        // Activate RizqMall for customer (no subscription check)
        if (!$user->rizqmall_activated_at) {
            $user->rizqmall_activated_at = now();
            $user->save();
        }

        // Generate SSO token for customer
        $token = $this->generateSsoToken($user, 'customer');

        // Build redirect URL
        $rizqmallUrl = config('services.rizqmall.url', 'http://localhost:8001');
        $redirectUrl = $rizqmallUrl . '/auth/sso?token=' . $token;

        Log::info('Customer SSO redirect', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect($redirectUrl);
    }

    /**
     * Generate encrypted SSO token
     */
    private function generateSsoToken(User $user, string $userType = 'customer')
    {
        // Get active account data (for vendors)
        $account = null;
        $accountStatus = 'none';
        $accountExpiresAt = null;

        if ($userType === 'vendor') {
            $account = $user->accounts()
                ->where('type', 'rizqmall')
                ->where('active', 1)
                ->first();

            if ($account) {
                $accountStatus = 'active';
                $accountExpiresAt = $account->expires_at;
            }
        }

        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'user_type' => $userType,
            'subscription_status' => $accountStatus,
            'subscription_expires_at' => $accountExpiresAt,
            'stores_quota' => $user->rizqmall_stores_quota ?? ($userType === 'vendor' ? 1 : 0),
            'timestamp' => now()->timestamp,
            'expires_at' => now()->addHours(1)->timestamp,
            'nonce' => Str::random(32),
        ];

        // Encrypt payload
        $jsonPayload = json_encode($payload);
        $encrypted = encrypt($jsonPayload);

        // Base64 encode for URL safety
        return base64_encode($encrypted);
    }

    /**
     * Validate SSO token (API endpoint for RizqMall)
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            // Decode and decrypt token
            $encrypted = base64_decode($request->token);
            $jsonPayload = decrypt($encrypted);
            $payload = json_decode($jsonPayload, true);

            // Validate expiry
            if ($payload['expires_at'] < now()->timestamp) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Token expired',
                ], 401);
            }

            // Validate user exists
            $user = User::find($payload['user_id']);
            if (!$user) {
                return response()->json([
                    'valid' => false,
                    'message' => 'User not found',
                ], 404);
            }

            // Return user data
            return response()->json([
                'valid' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'phone' => $user->profile?->phone_number ?? null,
                    'user_type' => $payload['user_type'],
                    'subscription_status' => $payload['subscription_status'],
                    'subscription_expires_at' => $payload['subscription_expires_at'],
                    'stores_quota' => $payload['stores_quota'],
                    'avatar' => $user->profile?->avatar ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SSO token validation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Invalid token',
            ], 401);
        }
    }

    /**
     * Get user data (API endpoint for RizqMall)
     */
    public function getUserData(Request $request, $userId)
    {
        $user = User::with(['profile', 'accounts'])->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $rizqmallAccount = $user->accounts()
            ->where('type', 'rizqmall')
            ->where('active', 1)
            ->first();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->profile?->phone_number ?? null,
                'avatar' => $user->profile?->avatar ?? null,
                'subscription_status' => $rizqmallAccount ? 'active' : 'none',
                'subscription_expires_at' => $rizqmallAccount?->expires_at ?? null,
                'stores_quota' => $user->rizqmall_stores_quota ?? 0,
                'rizqmall_activated_at' => $user->rizqmall_activated_at,
                'has_rizqmall_subscription' => $rizqmallAccount && $rizqmallAccount->active,
            ],
        ]);
    }

    /**
     * Verify subscription status (API endpoint for RizqMall)
     */
    public function verifySubscription(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $rizqmallAccount = $user->accounts()
            ->where('type', 'rizqmall')
            ->where('active', 1)
            ->first();

        return response()->json([
            'success' => true,
            'has_active_subscription' => $rizqmallAccount !== null,
            'subscription' => $rizqmallAccount ? [
                'status' => 'active',
                'plan' => 'rizqmall',
                'start_date' => $rizqmallAccount->created_at->toIso8601String(),
                'end_date' => $rizqmallAccount->expires_at,
            ] : null,
        ]);
    }

    /**
     * Handle logout webhook from RizqMall
     */
    public function handleLogout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        Log::info('Logout webhook from RizqMall', [
            'user_id' => $request->user_id,
        ]);

        // You can add additional logout logic here if needed
        // For example, invalidate sessions, clear caches, etc.

        return response()->json([
            'success' => true,
            'message' => 'Logout acknowledged',
        ]);
    }

    /**
     * Update store quota (when user purchases additional stores)
     */
    public function updateStoreQuota(Request $request, $userId)
    {
        $request->validate([
            'quota' => 'required|integer|min:1',
        ]);

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->rizqmall_stores_quota = $request->quota;
        $user->save();

        Log::info('Store quota updated', [
            'user_id' => $userId,
            'new_quota' => $request->quota,
        ]);

        return response()->json([
            'success' => true,
            'quota' => $user->rizqmall_stores_quota,
        ]);
    }
}
