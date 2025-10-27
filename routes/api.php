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
    Route::get('/users/{id}', [UserApiController::class, 'show']);
    Route::get('/users/{id}/profile', [UserApiController::class, 'profile']);
    
    // Subscription endpoints
    Route::get('/subscriptions/verify/{userId}', [SubscriptionApiController::class, 'verify']);
    Route::get('/subscriptions/{userId}/status', [SubscriptionApiController::class, 'status']);
    
    // Token validation
    Route::post('/auth/validate', [UserApiController::class, 'validateToken']);
    
    // Webhook from RizqMall (for events like store_created)
    Route::post('/webhooks/rizqmall', [WebhookController::class, 'handleRizqmall']);
});