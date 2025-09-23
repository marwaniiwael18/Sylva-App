<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate user and return user data with token
     */
    public function login(array $credentials): array
    {
        // For demo purposes, check demo credentials
        if ($credentials['email'] === 'demo@sylva.com' && $credentials['password'] === 'demo123') {
            $user = $this->getDemoUser();
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => new UserResource($user),
                'token' => $this->generateToken($user)
            ];
        }

        // In a real application, you would:
        // 1. Find user by email
        // 2. Check password hash
        // 3. Generate actual token (using Sanctum or JWT)
        
        throw ValidationException::withMessages([
            'email' => 'The provided credentials are incorrect.',
        ]);
    }

    /**
     * Register a new user
     */
    public function register(array $userData): array
    {
        // In a real application, you would create the user in database
        $user = (object) [
            'id' => rand(2, 1000),
            'name' => $userData['name'],
            'email' => $userData['email'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return [
            'success' => true,
            'message' => 'Registration successful',
            'user' => new UserResource($user),
            'token' => $this->generateToken($user)
        ];
    }

    /**
     * Generate demo token
     */
    private function generateToken($user): string
    {
        return 'demo_token_' . $user->id . '_' . time();
    }

    /**
     * Get demo user data
     */
    private function getDemoUser()
    {
        return (object) [
            'id' => 1,
            'name' => 'Sarah Johnson',
            'email' => 'demo@sylva.com',
            'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b5bb?ixlib=rb-4.0.3',
            'created_at' => now()->subMonths(6),
            'updated_at' => now(),
            'stats' => (object) [
                'trees_planted' => 127,
                'events_attended' => 23,
                'projects_joined' => 8,
                'impact_score' => 847
            ]
        ];
    }

    /**
     * Logout user
     */
    public function logout(): array
    {
        // In a real application, you would revoke the token
        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email): array
    {
        // In a real application, you would:
        // 1. Generate password reset token
        // 2. Send email with reset link
        
        return [
            'success' => true,
            'message' => 'Password reset email sent successfully'
        ];
    }
}