<?php

// routes/web.php
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ReferralController;

use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToyyibPayController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\UserImportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Auth\RegisterPlusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('/payment/callback-test', [SubscriptionController::class, 'callbackTest']);
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
    Route::get('/admin/user/{id}/details', [UserRoleController::class, 'details'])
        ->name('admin.user.details');
    Route::post('/admin/users/{user}/assign-referral', [UserRoleController::class, 'assignReferral'])
    ->name('admin.users.assignReferral');
    Route::get('/admin/users/referral-list', [UserRoleController::class, 'referralList'])
    ->name('admin.users.referralList');
    Route::post('/admin/users/{user}/remove-referral', [UserRoleController::class, 'removeReferral'])
    ->name('admin.users.removeReferral');


    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('index');
        Route::get('create', [UserRoleController::class, 'create'])->name('create');
        Route::post('store', [UserRoleController::class, 'store'])->name('store');
        Route::post('/import', [UserImportController::class, 'import'])->name('import');
        Route::get('/blacklists', [UserRoleController::class, 'blacklists'])->name('blacklists');
        Route::post('/addToBlacklist/{user}', [UserRoleController::class, 'addToBlacklist'])->name('addToBlacklist');

    
        // show user details
        Route::get('{user}', [UserRoleController::class, 'show'])->name('show');
        Route::get('{user}/edit', [UserRoleController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserRoleController::class, 'update'])->name('update');

        Route::post('/user/{id}/redeem/{type}', [UserRoleController::class, 'redeemCollection'])->name('collection.redeem');
    
        // action endpoints (AJAX-friendly)
        Route::post('{user}/toggle-admin', [UserRoleController::class, 'toggleAdminAjax'])->name('toggleAdminAjax');
        Route::post('{user}/account/{account}/toggle-active', [UserRoleController::class, 'toggleAccountActive'])->name('toggleAccountActive');
        Route::post('{user}/account/{account}/update-serial', [UserRoleController::class, 'updateAccountSerial'])->name('updateAccountSerial');
    
        // referral trees
        Route::get('{user}/referrals/tree', [UserRoleController::class, 'referralTree'])->name('referralTree');
        Route::get('{user}/sandbox-referrals/tree', [UserRoleController::class, 'sandboxReferralTree'])->name('sandboxReferralTree');
    
        // reuse check routes you already have
        Route::post('check-serial', [UserRoleController::class, 'checkSerial'])->name('checkSerial');
        Route::post('check-email', [UserRoleController::class, 'checkEmail'])->name('checkEmail');
    
        // (existing) assign/remove referral routes...
    });

    Route::post('/admin/users/{user}/delete', [UserRoleController::class, 'destroy'])
    ->name('admin.users.destroy');

    // Subscription actions

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.users.index');
    Route::get('/collection', [CollectionController::class, 'index'])->name('collection.index');

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
    Route::post('/profile/bank', [ProfileController::class, 'updateBank'])->name('profile.bank');
    Route::post('/profile/course', [ProfileController::class, 'updateCourse'])->name('profile.course');
    Route::post('/profile/nextofkin', [ProfileController::class, 'updateNextOfKin'])->name('profile.nextofkin');
    Route::post('/profile/pewaris/store', [ProfileController::class, 'storePewaris'])->name('profile.pewaris.store');
    Route::post('/profile/affiliation', [ProfileController::class, 'updateAffiliation'])->name('profile.affiliation');

    Route::get('/setup-store', [ProfileController::class, 'redirectToRizqmall'])->name('setup.store');

});

Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe.plan');
Route::post('/payment/callback', [SubscriptionController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/payment/return', [SubscriptionController::class, 'paymentReturn'])->name('payment.return');
Route::get('/subscriptions/history', [SubscriptionController::class, 'history'])->name('subscriptions.history');
Route::post('/subscribe/pay-next/{subscription}', [SubscriptionController::class, 'payNextInstallment'])
    ->name('subscriptions.payNext');


