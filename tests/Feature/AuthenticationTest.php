<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/signup', $userData);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->is_moderator);
    }

    #[Test]
    public function user_cannot_register_with_invalid_data()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ];

        $response = $this->post('/signup', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
    }

    #[Test]
    public function user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/signup', $userData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(1, User::where('email', 'existing@example.com')->count());
    }

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_cannot_login_with_incorrect_credentials()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
              ->post('/logout')
              ->assertRedirect('/login');        $this->assertGuest();
    }

    #[Test]
    public function authenticated_user_can_access_protected_routes()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->get('/dashboard')
             ->assertStatus(200);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $this->get('/dashboard')
             ->assertRedirect('/login');
    }

    #[Test]
    public function admin_user_has_admin_role_methods()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->canValidateReports());
    }

    #[Test]
    public function moderator_user_has_moderator_role_methods()
    {
        $moderator = User::factory()->create(['is_moderator' => true]);

        $this->assertTrue($moderator->isModerator());
        $this->assertTrue($moderator->canValidateReports());
    }

    #[Test]
    public function regular_user_does_not_have_admin_privileges()
    {
        $user = User::factory()->create(['is_admin' => false, 'is_moderator' => false]);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isModerator());
        $this->assertFalse($user->canValidateReports());
    }
}