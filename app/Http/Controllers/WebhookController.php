<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle webhooks from RizqMall
     * POST /api/webhooks/rizqmall
     */
    public function handleRizqmall(Request $request)
    {
        $event = $request->input('event');
        $data = $request->input('data');
        $timestamp = $request->input('timestamp');

        Log::info('RizqMall webhook received', [
            'event' => $event,
            'data' => $data,
            'timestamp' => $timestamp,
        ]);

        // Handle different event types
        switch ($event) {
            case 'store_created':
                return $this->handleStoreCreated($data);
                
            case 'user_logout':
                return $this->handleUserLogout($data);
                
            case 'order_placed':
                return $this->handleOrderPlaced($data);
                
            case 'subscription_check':
                return $this->handleSubscriptionCheck($data);
                
            default:
                Log::warning('Unknown webhook event', ['event' => $event]);
                return response()->json([
                    'success' => true,
                    'message' => 'Event received but not handled',
                ]);
        }
    }

    /**
     * Handle store_created event
     */
    private function handleStoreCreated($data)
    {
        $userId = $data['user_id'] ?? null;
        $storeName = $data['store_name'] ?? 'Unknown Store';

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                Log::info('Store created notification', [
                    'user_id' => $userId,
                    'user_email' => $user->email,
                    'store_name' => $storeName,
                ]);

                // You can send email notification, update stats, etc.
                // Example: Mail::to($user)->send(new StoreCreatedNotification($storeName));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Store creation logged',
        ]);
    }

    /**
     * Handle user_logout event
     */
    private function handleUserLogout($data)
    {
        $userId = $data['user_id'] ?? null;

        Log::info('User logout from RizqMall', [
            'user_id' => $userId,
            'timestamp' => $data['timestamp'] ?? now(),
        ]);

        // Optional: Track user activity, update last seen, etc.

        return response()->json([
            'success' => true,
            'message' => 'Logout processed',
        ]);
    }

    /**
     * Handle order_placed event
     */
    private function handleOrderPlaced($data)
    {
        $userId = $data['user_id'] ?? null;
        $orderNumber = $data['order_number'] ?? null;
        $amount = $data['amount'] ?? 0;

        Log::info('Order placed notification', [
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'amount' => $amount,
        ]);

        // You can update user statistics, send notifications, etc.

        return response()->json([
            'success' => true,
            'message' => 'Order notification received',
        ]);
    }

    /**
     * Handle subscription_check event
     */
    private function handleSubscriptionCheck($data)
    {
        $userId = $data['user_id'] ?? null;

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID required',
            ], 400);
        }

        $user = User::with('accounts')->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $rizqmallAccount = $user->accounts()->where('type', 'rizqmall')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'has_subscription' => (bool) $rizqmallAccount,
                'is_active' => $rizqmallAccount && $rizqmallAccount->active,
                'expires_at' => $rizqmallAccount ? $rizqmallAccount->expires_at : null,
            ],
        ]);
    }
}