<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donation;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_view_donations_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->get('/donations')
             ->assertStatus(200)
             ->assertSee('Donations');
    }

    #[Test]
    public function authenticated_user_can_create_donation()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $donationData = [
            'amount' => 50.00,
            'currency' => 'EUR',
            'type' => 'tree_planting',
            'event_id' => $event->id,
            'message' => 'Supporting reforestation efforts',
            'anonymous' => false,
        ];

        $this->actingAs($user)
             ->post('/donations', $donationData)
             ->assertRedirect();

        $this->assertDatabaseHas('donations', [
            'user_id' => $user->id,
            'amount' => 50.00,
            'type' => 'tree_planting',
            'event_id' => $event->id,
            'message' => 'Supporting reforestation efforts',
            'anonymous' => false,
        ]);
    }

    #[Test]
    public function donation_requires_valid_amount()
    {
        $user = User::factory()->create();

        $donationData = [
            'amount' => -10.00, // Invalid negative amount
            'currency' => 'EUR',
            'type' => 'tree_planting',
        ];

        $this->actingAs($user)
             ->post('/donations', $donationData)
             ->assertSessionHasErrors('amount');
    }

    #[Test]
    public function donation_requires_valid_type()
    {
        $user = User::factory()->create();

        $donationData = [
            'amount' => 25.00,
            'currency' => 'EUR',
            'type' => 'invalid_type', // Invalid type
        ];

        $this->actingAs($user)
             ->post('/donations', $donationData)
             ->assertSessionHasErrors('type');
    }

    #[Test]
    public function user_can_view_own_donations()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create donations for both users (successful ones for total count)
        Donation::factory()->create(['user_id' => $user->id, 'amount' => 100.00, 'payment_status' => 'succeeded']);
        Donation::factory()->create(['user_id' => $user->id, 'amount' => 50.00, 'payment_status' => 'succeeded']);
        Donation::factory()->create(['user_id' => $otherUser->id, 'amount' => 75.00, 'payment_status' => 'succeeded']);

        $response = $this->actingAs($user)->get('/donations');

        $response->assertStatus(200);
        // Should only see user's own donations
        $this->assertEquals(150.00, $user->fresh()->total_donations);
    }

    #[Test]
    public function donation_payment_status_defaults_to_pending()
    {
        $user = User::factory()->create();

        $donation = Donation::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('pending', $donation->payment_status);
    }

    #[Test]
    public function successful_donations_are_counted_in_total()
    {
        $user = User::factory()->create();

        // Create successful donations
        Donation::factory()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'payment_status' => 'succeeded'
        ]);
        Donation::factory()->create([
            'user_id' => $user->id,
            'amount' => 50.00,
            'payment_status' => 'succeeded'
        ]);

        // Create failed donation (should not be counted)
        Donation::factory()->create([
            'user_id' => $user->id,
            'amount' => 25.00,
            'payment_status' => 'failed'
        ]);

        $this->assertEquals(150.00, $user->fresh()->total_donations);
    }

    #[Test]
    public function donation_can_be_made_anonymous()
    {
        $user = User::factory()->create();

        $donation = Donation::factory()->create([
            'user_id' => $user->id,
            'anonymous' => true
        ]);

        $this->assertTrue($donation->anonymous);
    }

    #[Test]
    public function donation_has_formatted_amount_attribute()
    {
        $donation = Donation::factory()->create([
            'amount' => 123.45,
            'currency' => 'EUR'
        ]);

        $this->assertEquals('123.45 EUR', $donation->formatted_amount);
    }

    #[Test]
    public function donation_has_type_name_attribute()
    {
        $donation = Donation::factory()->create(['type' => 'tree_planting']);

        $this->assertEquals('Tree Planting', $donation->type_name);
    }

    #[Test]
    public function donation_has_payment_status_name_attribute()
    {
        $donation = Donation::factory()->create(['payment_status' => 'succeeded']);

        $this->assertEquals('Succeeded', $donation->payment_status_name);
    }

    #[Test]
    public function donation_scopes_work_correctly()
    {
        $user = User::factory()->create();

        // Create donations with different statuses
        Donation::factory()->create(['user_id' => $user->id, 'payment_status' => 'succeeded']);
        Donation::factory()->create(['user_id' => $user->id, 'payment_status' => 'pending']);
        Donation::factory()->create(['user_id' => $user->id, 'payment_status' => 'failed']);

        $successfulDonations = Donation::successful()->forUser($user->id)->get();
        $this->assertCount(1, $successfulDonations);
        $this->assertEquals('succeeded', $successfulDonations->first()->payment_status);
    }

    #[Test]
    public function donation_can_be_filtered_by_type()
    {
        $user = User::factory()->create();

        Donation::factory()->create(['user_id' => $user->id, 'type' => 'tree_planting']);
        Donation::factory()->create(['user_id' => $user->id, 'type' => 'maintenance']);
        Donation::factory()->create(['user_id' => $user->id, 'type' => 'awareness']);

        $treePlantingDonations = Donation::byType('tree_planting')->forUser($user->id)->get();
        $this->assertCount(1, $treePlantingDonations);
        $this->assertEquals('tree_planting', $treePlantingDonations->first()->type);
    }
}