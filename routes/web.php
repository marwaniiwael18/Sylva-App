<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\ForumController;

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
});
