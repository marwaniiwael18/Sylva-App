<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Event;
use App\Models\Donation;
use App\Models\Tree;
use App\Models\ForumPost;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_correct_fillable_attributes()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'is_moderator' => false,
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue($user->is_admin);
        $this->assertFalse($user->is_moderator);
    }

    /** @test */
    public function user_has_correct_hidden_attributes()
    {
        $user = User::factory()->create();

        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function user_has_correct_default_attributes()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->is_moderator);
    }

    /** @test */
    public function user_password_is_hashed()
    {
        $user = User::factory()->create(['password' => 'plainpassword']);

        $this->assertNotEquals('plainpassword', $user->password);
        $this->assertTrue(password_verify('plainpassword', $user->password));
    }

    /** @test */
    public function is_admin_returns_correct_value()
    {
        $regularUser = User::factory()->create(['is_admin' => false]);
        $adminUser = User::factory()->create(['is_admin' => true]);

        $this->assertFalse($regularUser->isAdmin());
        $this->assertTrue($adminUser->isAdmin());
    }

    /** @test */
    public function is_moderator_returns_correct_value()
    {
        $regularUser = User::factory()->create(['is_moderator' => false]);
        $moderatorUser = User::factory()->create(['is_moderator' => true]);

        $this->assertFalse($regularUser->isModerator());
        $this->assertTrue($moderatorUser->isModerator());
    }

    /** @test */
    public function can_validate_reports_returns_correct_value()
    {
        $regularUser = User::factory()->create(['is_admin' => false, 'is_moderator' => false]);
        $moderatorUser = User::factory()->create(['is_admin' => false, 'is_moderator' => true]);
        $adminUser = User::factory()->create(['is_admin' => true, 'is_moderator' => false]);

        $this->assertFalse($regularUser->canValidateReports());
        $this->assertTrue($moderatorUser->canValidateReports());
        $this->assertTrue($adminUser->canValidateReports());
    }

    /** @test */
    public function user_has_many_forum_posts()
    {
        $user = User::factory()->create();

        ForumPost::factory()->create(['author_id' => $user->id]);
        ForumPost::factory()->create(['author_id' => $user->id]);

        $this->assertCount(2, $user->fresh()->forumPosts);
    }

    /** @test */
    public function user_has_many_comments()
    {
        $user = User::factory()->create();

        Comment::factory()->create(['author_id' => $user->id]);
        Comment::factory()->create(['author_id' => $user->id]);

        $this->assertCount(2, $user->fresh()->comments);
    }

    /** @test */
    public function user_has_many_organized_events()
    {
        $user = User::factory()->create();

        Event::factory()->create(['organized_by_user_id' => $user->id]);
        Event::factory()->create(['organized_by_user_id' => $user->id]);

        $this->assertCount(2, $user->fresh()->organizedEvents);
    }

    /** @test */
    public function user_belongs_to_many_participating_events()
    {
        $user = User::factory()->create();
        $event1 = Event::factory()->create();
        $event2 = Event::factory()->create();

        $user->participatingEvents()->attach($event1);
        $user->participatingEvents()->attach($event2);

        $this->assertCount(2, $user->fresh()->participatingEvents);
    }

    /** @test */
    public function user_has_many_donations()
    {
        $user = User::factory()->create();

        Donation::factory()->create(['user_id' => $user->id]);
        Donation::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->fresh()->donations);
    }

    /** @test */
    public function user_has_many_planted_trees()
    {
        $user = User::factory()->create();

        Tree::factory()->create(['planted_by_user' => $user->id]);
        Tree::factory()->create(['planted_by_user' => $user->id]);

        $this->assertCount(2, $user->fresh()->plantedTrees);
    }

    /** @test */
    public function get_total_donations_attribute_calculates_correctly()
    {
        $user = User::factory()->create();

        // Create successful donations
        Donation::factory()->create(['user_id' => $user->id, 'amount' => 100.00, 'payment_status' => 'succeeded']);
        Donation::factory()->create(['user_id' => $user->id, 'amount' => 50.00, 'payment_status' => 'succeeded']);

        // Create failed donation (should not be included)
        Donation::factory()->create(['user_id' => $user->id, 'amount' => 25.00, 'payment_status' => 'failed']);

        $this->assertEquals(150.00, $user->total_donations);
    }
}