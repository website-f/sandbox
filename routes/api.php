<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\SubscriptionApiController;

/*
|--------------------------------------------------------------------------
| API Routes for RizqMall Integration
|--------------------------------------------------------------------------
*/

// Middleware to verify API key
Route::middleware(['api.key'])->group(function () {
    
    // User endpoints
    Route::prefix('users')->group(function () {
        Route::get('/{id}', [UserApiController::class, 'show']);
        Route::get('/{id}/profile', [UserApiController::class, 'profile']);
    });

    // Subscription endpoints
    Route::prefix('subscriptions')->group(function () {
        Route::get('/verify/{userId}', [SubscriptionApiController::class, 'verify']);
        Route::get('/{userId}/status', [SubscriptionApiController::class, 'status']);
    });

    // Auth validation
    Route::post('/auth/validate', [UserApiController::class, 'validateToken']);
});

// Webhook from RizqMall (different authentication)
Route::post('/webhooks/rizqmall', [\App\Http\Controllers\RizqMallController::class, 'webhook']);