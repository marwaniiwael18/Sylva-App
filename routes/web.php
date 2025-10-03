<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\EventController;
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
    Route::get('/community-feed', [WebController::class, 'communityFeed'])->name('community.feed');

    
    // Forum Routes
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{forumPost}', [ForumController::class, 'show'])->name('forum.show');
    Route::get('/forum/{forumPost}/edit', [ForumController::class, 'edit'])->name('forum.edit');
    Route::put('/forum/{forumPost}', [ForumController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{forumPost}', [ForumController::class, 'destroy'])->name('forum.destroy');
    Route::post('/forum/{forumPost}/comments', [ForumController::class, 'storeComment'])->name('forum.comments.store');
    Route::put('/comments/{comment}', [ForumController::class, 'updateComment'])->name('forum.comments.update');
    Route::delete('/comments/{comment}', [ForumController::class, 'destroyComment'])->name('forum.comments.destroy');
    Route::get('/forum/filter/event', [ForumController::class, 'filterByEvent'])->name('forum.filter.event');


    
    // Event Routes
    Route::resource('events', EventController::class);
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my-events');
    Route::post('/events/{event}/join', [EventController::class, 'join'])->name('events.join');
    Route::delete('/events/{event}/leave', [EventController::class, 'leave'])->name('events.leave');


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

    // Admin Routes - Restricted to administrators only
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
        Route::patch('/reports/{report}/validate', [AdminController::class, 'validateReport'])->name('reports.validate');
    });

});
