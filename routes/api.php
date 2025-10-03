<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;

use App\Http\Controllers\Api\EventController as ApiEventController;
use App\Http\Controllers\Api\EventController;

use App\Http\Controllers\Api\DonationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Reports CRUD
    Route::apiResource('reports', ReportController::class);
    Route::post('reports/{report}/validate', [ReportController::class, 'validate']);
    Route::get('reports-statistics', [ReportController::class, 'statistics']);
    

    // Events CRUD
    Route::apiResource('events', ApiEventController::class)->names([
        'index' => 'api.events.index',
        'store' => 'api.events.store',
        'show' => 'api.events.show',
        'update' => 'api.events.update',
        'destroy' => 'api.events.destroy',
    ]);
    Route::get('my-events', [ApiEventController::class, 'myEvents'])->name('api.events.my-events');
    Route::post('events/{event}/join', [ApiEventController::class, 'join'])->name('api.events.join');
    Route::delete('events/{event}/leave', [ApiEventController::class, 'leave'])->name('api.events.leave');
    Route::get('events-statistics', [ApiEventController::class, 'statistics'])->name('api.events.statistics');


    // Trees CRUD
    Route::apiResource('trees', \App\Http\Controllers\TreeController::class);
    Route::get('trees/map/data', [\App\Http\Controllers\TreeController::class, 'mapData']);
    Route::get('trees/user/my', [\App\Http\Controllers\TreeController::class, 'myTrees']);

    // Donations API
    Route::prefix('donations')->name('api.donations.')->group(function () {
        Route::get('/', [DonationController::class, 'index']);
        Route::post('/', [DonationController::class, 'store']);
        Route::get('/{donation}', [DonationController::class, 'show']);
        Route::post('/{donation}/create-payment-intent', [DonationController::class, 'createPaymentIntent']);
        Route::post('/{donation}/refund', [DonationController::class, 'refund']);
        Route::delete('/{donation}', [DonationController::class, 'destroy']);
        Route::get('/user/statistics', [DonationController::class, 'userStatistics']);
    });

});

// Stripe Webhook (public route - no authentication required)
Route::post('donations/stripe/webhook', [DonationController::class, 'handleStripeWebhook'])->name('api.donations.stripe.webhook');

// Temporary public routes for testing (remove in production)
Route::get('reports-public', [ReportController::class, 'index']);
Route::post('reports-public', [ReportController::class, 'store']);
Route::get('reports-public/{report}', [ReportController::class, 'show']);
Route::put('reports-public/{report}', [ReportController::class, 'update']);
Route::delete('reports-public/{report}', [ReportController::class, 'destroy']);
Route::post('reports-public/{report}/validate', [ReportController::class, 'validate']);
Route::get('reports-statistics-public', [ReportController::class, 'statistics']);

// Mock API endpoints for development
Route::middleware('cors')->group(function () {
    // Dashboard stats
    Route::get('dashboard/stats', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'trees_planted' => 127,
                'co2_saved' => '2.3 tons',
                'impact_score' => 847
            ]
        ]);
    });
});