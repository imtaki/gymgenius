<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealTest extends TestCase
{
    use RefreshDatabase;

    // Index tests
    public function test_user_can_list_meals_for_a_specific_day(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        Meal::factory()->count(3)->for($user)->for($dailyLog)->create();

        $response = $this->actingAs($user)->getJson("/api/meals/user/{$user->id}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJsonCount(3, 'data');
    }

    public function test_user_can_only_see_their_own_meals(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $dailyLog1 = DailyLog::factory()->for($user1)->create();
        $dailyLog2 = DailyLog::factory()->for($user2)->create();

        Meal::factory()->count(2)->for($user1)->for($dailyLog1)->create();
        Meal::factory()->count(3)->for($user2)->for($dailyLog2)->create();

        $response = $this->actingAs($user1)->getJson("/api/meals/user/{$user1->id}");

        $response->assertJsonCount(2, 'data');
    }

    public function test_user_cannot_view_another_users_meals(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->getJson("/api/meals/user/{$user2->id}");

        $response->assertForbidden();
    }

    // Show tests
    public function test_user_can_view_their_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $response = $this->actingAs($user)->getJson("/api/meals/{$meal->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $meal->id);
        $response->assertJsonPath('data.name', $meal->name);
    }

    public function test_user_cannot_view_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $response = $this->actingAs($user2)->getJson("/api/meals/{$meal->id}");

        $response->assertForbidden();
    }

    // Store tests
    public function test_user_can_create_meal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
            'name' => 'Chicken Breast',
            'category' => 'Protein',
            'calories' => 165,
            'protein' => 31,
            'carbs' => 0,
            'fats' => 3.6,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('meals', [
            'name' => 'Chicken Breast',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_meal_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'calories']);
    }

    public function test_create_meal_validates_numeric_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
            'name' => 'Meal',
            'calories' => 'not-a-number',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['calories']);
    }

    public function test_user_cannot_create_meal_for_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->postJson("/api/meals/user/{$user2->id}", [
            'name' => 'Meal',
            'calories' => 100,
        ]);

        $response->assertForbidden();
    }

    // Update tests
    public function test_user_can_update_their_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create([
            'name' => 'Chicken',
            'calories' => 165,
        ]);

        $response = $this->actingAs($user)->putJson("/api/meals/{$meal->id}", [
            'name' => 'Grilled Chicken',
            'calories' => 170,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('meals', [
            'id' => $meal->id,
            'name' => 'Grilled Chicken',
            'calories' => 170,
        ]);
    }

    public function test_user_cannot_update_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $response = $this->actingAs($user2)->putJson("/api/meals/{$meal->id}", [
            'name' => 'Updated',
            'calories' => 100,
        ]);

        $response->assertForbidden();
    }

    // Destroy tests
    public function test_user_can_delete_their_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $response = $this->actingAs($user)->deleteJson("/api/meals/{$meal->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
    }

    public function test_user_cannot_delete_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $response = $this->actingAs($user2)->deleteJson("/api/meals/{$meal->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('meals', ['id' => $meal->id]);
    }

    // Rate limiting tests
    public function test_meal_creation_is_rate_limited_to_30_requests_per_minute(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 30; $i++) {
            $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
                'name' => "Meal $i",
                'calories' => 100,
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
            'name' => 'Meal 31',
            'calories' => 100,
        ]);
        $this->assertEquals(429, $response->status());
    }
}
