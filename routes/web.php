<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\BlogAIController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Metrics endpoint for Prometheus monitoring
Route::get('/metrics', [App\Http\Controllers\MetricsController::class, 'index']);

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

    
    // Blog Routes
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/blog/{blogPost}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{blogPost}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{blogPost}', [BlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('/blog/{blogPost}/comments', [BlogController::class, 'storeComment'])->name('blog.comments.store');
    Route::put('/comments/{comment}', [BlogController::class, 'updateComment'])->name('blog.comments.update');
    Route::delete('/comments/{comment}', [BlogController::class, 'destroyComment'])->name('blog.comments.destroy');
    Route::get('/blog/filter/event', [BlogController::class, 'filterByEvent'])->name('blog.filter.event');
    
    // AI Blog Routes
    Route::get('/blog/ai/create', [BlogAIController::class, 'create'])->name('blog.ai.create');
    Route::post('/blog/ai/generate', [BlogAIController::class, 'generate'])->name('blog.ai.generate');
    Route::post('/blog/ai/store', [BlogAIController::class, 'store'])->name('blog.ai.store');
    Route::post('/blog/ai/improve', [BlogAIController::class, 'improve'])->name('blog.ai.improve');

    
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

        
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
        Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.update-status');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        
        // Reports Management  
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::patch('/reports/{report}/validate', [AdminController::class, 'validateReport'])->name('reports.validate');
        Route::patch('/reports/{report}/reject', [AdminController::class, 'rejectReport'])->name('reports.reject');
        Route::delete('/reports/{report}', [AdminController::class, 'deleteReport'])->name('reports.delete');
        
        // Events Management
        Route::get('/events', [AdminController::class, 'events'])->name('events');
        Route::patch('/events/{event}/featured', [AdminController::class, 'toggleEventFeatured'])->name('events.featured');
        Route::delete('/events/{event}', [AdminController::class, 'deleteEvent'])->name('events.delete');
        
        // Blog Management
        Route::get('/blog', [AdminController::class, 'blog'])->name('blog');
        Route::delete('/blog/{blogPost}', [AdminController::class, 'deleteBlogPost'])->name('blog.delete');
        Route::patch('/blog/{blogPost}/pin', [AdminController::class, 'pinBlogPost'])->name('blog.pin');
        
        // Donations Management
        Route::get('/donations', [AdminController::class, 'donations'])->name('donations');
        Route::post('/donations/{donation}/refund', [AdminController::class, 'processRefund'])->name('donations.refund.process');
        Route::post('/donations/{donation}/thank-you', [AdminController::class, 'generateThankYou'])->name('donations.thank-you');
        Route::post('/donations/{donation}/analyze-risk', [AdminController::class, 'analyzeRefundRisk'])->name('donations.analyze-risk');
        Route::post('/donations/campaign-recommendations', [AdminController::class, 'generateCampaignRecommendations'])->name('donations.campaign-recommendations');
        Route::post('/donations/export', [AdminController::class, 'exportDonations'])->name('donations.export');
        Route::post('/refunds/{refund}/approve', [AdminController::class, 'approveRefund'])->name('refunds.approve');
        Route::post('/refunds/{refund}/reject', [AdminController::class, 'rejectRefund'])->name('refunds.reject');
        
        // Trees Management
        Route::get('/trees', [AdminController::class, 'trees'])->name('trees');
        Route::patch('/trees/{tree}/verify', [AdminController::class, 'verifyTree'])->name('trees.verify');
        Route::delete('/trees/{tree}', [AdminController::class, 'deleteTree'])->name('trees.delete');
        
        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    });

});
