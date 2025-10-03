<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@sylva.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
        ]);

        // Create demo user
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@sylva.com',
            'password' => bcrypt('demo123'),
            'is_admin' => false,
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => false,
        ]);
    }
}
