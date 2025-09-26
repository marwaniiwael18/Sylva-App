<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;

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
    Route::get('/projects', [WebController::class, 'projects'])->name('projects');
    Route::get('/projects/{id}', [WebController::class, 'projectDetail'])->name('projects.detail');
    Route::get('/events', [WebController::class, 'events'])->name('events');
    Route::get('/events/{id}', [WebController::class, 'eventDetail'])->name('events.detail');
    Route::get('/feedback', [WebController::class, 'feedback'])->name('feedback');
    Route::get('/impact', [WebController::class, 'impact'])->name('impact');
});
