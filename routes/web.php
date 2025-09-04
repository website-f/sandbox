<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ToyyibPayController;
use App\Http\Controllers\UserImportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Auth\RegisterPlusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Login routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
    
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/users/import', [UserImportController::class, 'showForm'])->name('users.import.form');
Route::post('/users/import', [UserImportController::class, 'import'])->name('users.import');


Route::get('/register', [RegisterPlusController::class,'show'])->name('register'); // override Breeze’s view if needed
Route::post('/register', [RegisterPlusController::class,'store'])->name('register.store');

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/referrals/tree', [ReferralController::class,'tree'])->name('referrals.tree'); // JSON for visualization
    Route::get('/referrals/qr', [ReferralController::class,'qr'])->name('referrals.qr');

    Route::get('/admin/users', [UserRoleController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/{user}/toggle-admin', [UserRoleController::class, 'toggleAdmin'])->name('admin.users.toggleAdmin');
    // Subscription actions
    Route::post('/subscribe/{plan}', [ToyyibPayController::class,'subscribe'])->name('subscribe.plan'); // plan: rizqmall|sandbox
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::post('/profile/business', [ProfileController::class, 'updateBusiness'])->name('profile.business');
    Route::post('/profile/education', [ProfileController::class, 'updateEducation'])->name('profile.education');
    Route::post('/profile/course', [ProfileController::class, 'updateCourse'])->name('profile.course');
    Route::post('/profile/nextofkin', [ProfileController::class, 'updateNextOfKin'])->name('profile.nextofkin');
    Route::post('/profile/affiliation', [ProfileController::class, 'updateAffiliation'])->name('profile.affiliation');

    Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe.plan');
    Route::post('/payment/callback', [SubscriptionController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('/payment/return', [SubscriptionController::class, 'paymentReturn'])->name('payment.return');
    Route::get('/subscriptions/history', [SubscriptionController::class, 'history'])->name('subscriptions.history');

});



// ToyyibPay redirects
Route::get('/payments/toyyib/return', [ToyyibPayController::class,'return'])->name('toyyib.return');
Route::post('/payments/toyyib/callback', [ToyyibPayController::class,'callback'])->name('toyyib.callback');

