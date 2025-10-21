<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tree;
use App\Models\TreeCare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TreeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_view_trees_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->get('/trees')
             ->assertStatus(200);
    }

    #[Test]
    public function authenticated_user_can_create_tree()
    {
        $user = User::factory()->create();

        $treeData = [
            'species' => 'Oak',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'planting_date' => '2024-01-15',
            'status' => 'Planted',
            'type' => 'Forest',
            'description' => 'A beautiful oak tree',
            'address' => 'Paris, France',
        ];

        $this->actingAs($user)
             ->post('/trees', $treeData)
             ->assertRedirect();

        $this->assertDatabaseHas('trees', [
            'species' => 'Oak',
            'planted_by_user' => $user->id,
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'status' => 'Planted',
            'type' => 'Forest',
        ]);
    }

    #[Test]
    public function tree_creation_requires_valid_coordinates()
    {
        $user = User::factory()->create();

        $treeData = [
            'species' => 'Oak',
            'latitude' => 91, // Invalid latitude (> 90)
            'longitude' => 2.3522,
            'planting_date' => '2024-01-15',
            'status' => 'Planted',
            'type' => 'Forest',
        ];

        $this->actingAs($user)
             ->post('/trees', $treeData)
             ->assertSessionHasErrors('latitude');
    }

    #[Test]
    public function user_can_view_own_planted_trees()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create trees for both users
        Tree::factory()->create(['planted_by_user' => $user->id]);
        Tree::factory()->create(['planted_by_user' => $user->id]);
        Tree::factory()->create(['planted_by_user' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/trees/user/my');

        $response->assertStatus(200);
        $this->assertCount(2, $user->fresh()->plantedTrees);
    }

    #[Test]
    public function tree_has_correct_default_status()
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create(['planted_by_user' => $user->id]);

        $this->assertEquals('Not Yet', $tree->status);
    }

    #[Test]
    public function tree_has_status_color_attribute()
    {
        $tree = Tree::factory()->create(['status' => 'Planted']);

        $this->assertEquals('green', $tree->status_color);
    }

    #[Test]
    public function tree_has_type_icon_attribute()
    {
        $tree = Tree::factory()->create(['type' => 'Fruit']);

        $this->assertEquals('🍎', $tree->type_icon);
    }

    #[Test]
    public function tree_has_formatted_planting_date_attribute()
    {
        $tree = Tree::factory()->create(['planting_date' => '2024-01-15']);

        $this->assertEquals('Jan 15, 2024', $tree->planting_date_formatted);
    }

    #[Test]
    public function tree_has_coordinates_attribute()
    {
        $tree = Tree::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $coordinates = $tree->coordinates;

        $this->assertEquals(48.8566, $coordinates['latitude']);
        $this->assertEquals(2.3522, $coordinates['longitude']);
    }

    #[Test]
    public function tree_scopes_work_correctly()
    {
        // Create trees with different statuses
        Tree::factory()->create(['status' => 'Planted']);
        Tree::factory()->create(['status' => 'Not Yet']);
        Tree::factory()->create(['status' => 'Sick']);
        Tree::factory()->create(['status' => 'Dead']);

        $plantedTrees = Tree::planted()->get();
        $notPlantedTrees = Tree::notPlanted()->get();
        $sickTrees = Tree::sick()->get();
        $deadTrees = Tree::dead()->get();

        $this->assertCount(1, $plantedTrees);
        $this->assertCount(1, $notPlantedTrees);
        $this->assertCount(1, $sickTrees);
        $this->assertCount(1, $deadTrees);
    }

    #[Test]
    public function tree_can_be_filtered_by_species()
    {
        Tree::factory()->create(['species' => 'Oak']);
        Tree::factory()->create(['species' => 'Pine']);
        Tree::factory()->create(['species' => 'Oak']);

        $oakTrees = Tree::bySpecies('Oak')->get();

        $this->assertCount(2, $oakTrees);
        $this->assertEquals('Oak', $oakTrees->first()->species);
    }

    #[Test]
    public function tree_can_be_filtered_by_type()
    {
        Tree::factory()->create(['type' => 'Fruit']);
        Tree::factory()->create(['type' => 'Forest']);
        Tree::factory()->create(['type' => 'Fruit']);

        $fruitTrees = Tree::byType('Fruit')->get();

        $this->assertCount(2, $fruitTrees);
        $this->assertEquals('Fruit', $fruitTrees->first()->type);
    }

    #[Test]
    public function tree_can_be_filtered_by_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Tree::factory()->create(['planted_by_user' => $user1->id]);
        Tree::factory()->create(['planted_by_user' => $user1->id]);
        Tree::factory()->create(['planted_by_user' => $user2->id]);

        $user1Trees = Tree::where('planted_by_user', $user1->id)->get();

        $this->assertCount(2, $user1Trees);
        $this->assertEquals($user1->id, $user1Trees->first()->planted_by_user);
    }

    #[Test]
    public function tree_has_care_relationship()
    {
        $tree = Tree::factory()->create();

        TreeCare::factory()->create(['tree_id' => $tree->id]);
        TreeCare::factory()->create(['tree_id' => $tree->id]);

        $this->assertCount(2, $tree->fresh()->careRecords);
        $this->assertEquals(2, $tree->care_count);
    }

    #[Test]
    public function tree_needs_care_when_no_recent_care()
    {
        $tree = Tree::factory()->create();

        $this->assertTrue($tree->needsCare());
    }

    #[Test]
    public function tree_does_not_need_care_when_recently_cared_for()
    {
        $tree = Tree::factory()->create();

        TreeCare::factory()->create([
            'tree_id' => $tree->id,
            'performed_at' => now()->subDays(3), // Within 7 days
        ]);

        $this->assertFalse($tree->fresh()->needsCare());
    }

    #[Test]
    public function tree_health_score_calculates_correctly()
    {
        $tree = Tree::factory()->create();

        TreeCare::factory()->create([
            'tree_id' => $tree->id,
            'condition_after' => 'excellent',
        ]);

        $this->assertEquals(100, $tree->fresh()->health_score);
    }
}