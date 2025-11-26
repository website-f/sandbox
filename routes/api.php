<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;

/*
|--------------------------------------------------------------------------
| API Routes for RizqMall Integration
|--------------------------------------------------------------------------
*/

// Middleware to verify API key from RizqMall
Route::middleware(['api'])->prefix('rizqmall')->group(function () {

    // SSO Token validation
    Route::post('/validate-token', [SsoController::class, 'validateToken']);

    // User data endpoints
    Route::get('/user/{userId}', [SsoController::class, 'getUserData']);

    // Subscription verification
    Route::get('/subscription/{userId}/verify', [SsoController::class, 'verifySubscription']);

    // Store quota management
    Route::get('/store-quota/{userId}', function ($userId) {
        $user = \App\Models\User::find($userId);
        return response()->json([
            'success' => true,
            'quota' => $user ? ($user->rizqmall_stores_quota ?? 0) : 0,
        ]);
    });

    Route::post('/store-quota/{userId}', [SsoController::class, 'updateStoreQuota']);

    // Logout webhook from RizqMall
    Route::post('/logout-webhook', [SsoController::class, 'handleLogout']);
});
