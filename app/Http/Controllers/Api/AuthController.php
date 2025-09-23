<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // For demo purposes, accept demo credentials
        if ($request->email === 'demo@sylva.com' && $request->password === 'demo123') {
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => 1,
                    'name' => 'Sarah Johnson',
                    'email' => 'demo@sylva.com',
                    'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b5bb?ixlib=rb-4.0.3',
                    'stats' => [
                        'treesPlanted' => 127,
                        'eventsAttended' => 23,
                        'projectsJoined' => 8,
                        'impactScore' => 847
                    ]
                ],
                'token' => 'demo_token_' . time()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Demo registration - just return success
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => rand(2, 1000),
                'name' => $request->name,
                'email' => $request->email,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($request->name),
                'stats' => [
                    'treesPlanted' => 0,
                    'eventsAttended' => 0,
                    'projectsJoined' => 0,
                    'impactScore' => 0
                ]
            ],
            'token' => 'new_user_token_' . time()
        ]);
    }

    public function logout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent successfully'
        ]);
    }
}