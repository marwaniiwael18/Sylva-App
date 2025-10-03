<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;

use App\Http\Controllers\TreeController;

use App\Http\Controllers\DonationController;


// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/map', [WebController::class, 'map'])->name('map');
    Route::get('/reports', [WebController::class, 'reports'])->name('reports');

    Route::resource('trees', TreeController::class);
    
    // Additional tree routes
    Route::get('/trees/map/data', [TreeController::class, 'mapData'])->name('trees.map.data');
    Route::get('/trees/user/my', [TreeController::class, 'myTrees'])->name('trees.my');

    // Donation Routes
    Route::prefix('donations')->name('donations.')->group(function () {
        Route::get('/', [DonationController::class, 'index'])->name('index');
        Route::get('/create', [DonationController::class, 'create'])->name('create');
        Route::post('/', [DonationController::class, 'store'])->name('store');
        Route::get('/{donation}', [DonationController::class, 'show'])->name('show');
        Route::get('/{donation}/payment', [DonationController::class, 'payment'])->name('payment');
        Route::get('/{donation}/payment-success', [DonationController::class, 'paymentSuccess'])->name('payment.success');
        Route::post('/{donation}/refund', [DonationController::class, 'refund'])->name('refund');
        Route::delete('/{donation}/cancel', [DonationController::class, 'cancel'])->name('cancel');
    });

});
