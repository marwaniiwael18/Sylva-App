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
    // Projects mock data
    Route::get('projects', function () {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'title' => 'Central Park Tree Restoration',
                    'description' => 'Restore native tree species in Central Park',
                    'location' => 'Central Park, NY',
                    'status' => 'active',
                    'progress' => 75,
                    'participants' => 45,
                    'target_participants' => 60,
                    'start_date' => '2024-01-15',
                    'end_date' => '2024-06-15'
                ],
                [
                    'id' => 2,
                    'title' => 'Brooklyn Rooftop Gardens',
                    'description' => 'Create rooftop gardens across Brooklyn',
                    'location' => 'Brooklyn, NY',
                    'status' => 'active',
                    'progress' => 45,
                    'participants' => 32,
                    'target_participants' => 50,
                    'start_date' => '2024-02-01',
                    'end_date' => '2024-08-01'
                ]
            ]
        ]);
    });

    // Events mock data
    Route::get('events', function () {
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'title' => 'Community Garden Workshop',
                    'description' => 'Learn sustainable gardening practices',
                    'location' => 'Brooklyn Community Center',
                    'date' => '2024-03-15',
                    'time' => '10:00',
                    'attendees' => 25,
                    'max_attendees' => 40
                ],
                [
                    'id' => 2,
                    'title' => 'Tree Planting Day',
                    'description' => 'Join us for a day of tree planting',
                    'location' => 'Prospect Park',
                    'date' => '2024-03-22',
                    'time' => '09:00',
                    'attendees' => 67,
                    'max_attendees' => 80
                ]
            ]
        ]);
    });

    // Dashboard stats
    Route::get('dashboard/stats', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'trees_planted' => 127,
                'events_attended' => 23,
                'projects_joined' => 8,
                'impact_score' => 847,
                'co2_saved' => '2.3 tons',
                'badges_earned' => 5
            ]
        ]);
    });
});