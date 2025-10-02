<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;

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
    
    // Trees CRUD
    Route::apiResource('trees', \App\Http\Controllers\TreeController::class);
    Route::get('trees/map/data', [\App\Http\Controllers\TreeController::class, 'mapData']);
    Route::get('trees/user/my', [\App\Http\Controllers\TreeController::class, 'myTrees']);
});

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