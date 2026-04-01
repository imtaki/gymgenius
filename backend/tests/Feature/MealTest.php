<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;

class MealTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Helper method to login a user and get JWT token
     */
    protected function loginAndGetToken(User $user, string $password = 'password'): string
    {
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        return $response->json('token');
    }

    // Index tests
    #[Test]
    #[Group('Meals.Index')]
    #[TestDox('Test that user can list meals for a specific day')]
    public function test_user_can_list_meals_for_a_specific_day(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $dailyLog = DailyLog::factory()->for($user)->create();
        Meal::factory()->count(3)->for($user)->for($dailyLog)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/meals/user/{$user->id}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJsonCount(3, 'data');
    }

    #[Test]
    #[Group('Meals.Index')]
    #[TestDox('Test that user can only see their own meals')]
    public function test_user_can_only_see_their_own_meals(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $dailyLog1 = DailyLog::factory()->for($user1)->create();
        $dailyLog2 = DailyLog::factory()->for($user2)->create();

        Meal::factory()->count(2)->for($user1)->for($dailyLog1)->create();
        Meal::factory()->count(3)->for($user2)->for($dailyLog2)->create();

        $token = $this->loginAndGetToken($user1);
        $response = $this->withToken($token)->getJson("/api/meals/user/{$user1->id}");

        $response->assertJsonCount(2, 'data');
    }

    #[Test]
    #[Group('Meals.Index')]
    #[TestDox('Test that user cannot view another user\'s meals')]
    public function test_user_cannot_view_another_users_meals(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user1);
        $response = $this->withToken($token)->getJson("/api/meals/user/{$user2->id}");

        $response->assertForbidden();
    }

    // Show tests
    #[Test]
    #[Group('Meals.Show')]
    #[TestDox('Test that user can view their own meal')]
    public function test_user_can_view_their_own_meal(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/meals/{$meal->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $meal->id);
        $response->assertJsonPath('data.name', $meal->name);
    }

    #[Test]
    #[Group('Meals.Show')]
    #[TestDox('Test that user cannot view another user\'s meal')]
    public function test_user_cannot_view_another_users_meal(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->getJson("/api/meals/{$meal->id}");

        $response->assertForbidden();
    }

    // Store tests
    #[Test]
    #[Group('Meals.Store')]
    #[TestDox('Test that user can create meal')]
    public function test_user_can_create_meal(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson("/api/meals/user/{$user->id}", [
            'name' => 'Chicken Breast',
            'category' => 'breakfast',
            'calories' => 165,
            'protein' => 31,
            'carbs' => 0,
            'fats' => 3,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('meals', [
            'name' => 'Chicken Breast',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    #[Group('Meals.Store')]
    #[TestDox('Test that create meal validates required fields')]
    public function test_create_meal_validates_required_fields(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson("/api/meals/user/{$user->id}", []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'calories']);
    }

    #[Test]
    #[Group('Meals.Store')]
    #[TestDox('Test that create meal validates numeric fields')]
    public function test_create_meal_validates_numeric_fields(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson("/api/meals/user/{$user->id}", [
            'name' => 'Meal',
            'calories' => 'not-a-number',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['calories']);
    }

    #[Test]
    #[Group('Meals.Store')]
    #[TestDox('Test that user cannot create meal for another user')]
    public function test_user_cannot_create_meal_for_another_user(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user1);
        $response = $this->withToken($token)->postJson("/api/meals/user/{$user2->id}", [
            'name' => 'Meal',
            'category' => 'breakfast',
            'calories' => 100,
            'protein' => 10,
            'carbs' => 5,
            'fats' => 3,
        ]);

        $response->assertForbidden();
    }

    // Update tests
    #[Test]
    #[Group('Meals.Update')]
    #[TestDox('Test that user can update their own meal')]
    public function test_user_can_update_their_own_meal(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create([
            'name' => 'Chicken',
            'calories' => 165,
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->putJson("/api/meals/{$meal->id}", [
            'name' => 'Grilled Chicken',
            'category' => 'lunch',
            'calories' => 170,
            'protein' => 32,
            'carbs' => 5,
            'fats' => 4,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('meals', [
            'id' => $meal->id,
            'name' => 'Grilled Chicken',
            'calories' => 170,
        ]);
    }

    #[Test]
    #[Group('Meals.Update')]
    #[TestDox('Test that user cannot update another user\'s meal')]
    public function test_user_cannot_update_another_users_meal(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->putJson("/api/meals/{$meal->id}", [
            'name' => 'Updated',
            'category' => 'dinner',
            'calories' => 100,
            'protein' => 10,
            'carbs' => 5,
            'fats' => 3,
        ]);

        $response->assertForbidden();
    }

    // Destroy tests
    #[Test]
    #[Group('Meals.Destroy')]
    #[TestDox('Test that user can delete their own meal')]
    public function test_user_can_delete_their_own_meal(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->deleteJson("/api/meals/{$meal->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
    }

    #[Test]
    #[Group('Meals.Destroy')]
    #[TestDox('Test that user cannot delete another user\'s meal')]
    public function test_user_cannot_delete_another_users_meal(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->deleteJson("/api/meals/{$meal->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('meals', ['id' => $meal->id]);
    }

    // Rate limiting tests
    #[Test]
    #[Group('Meals.RateLimit')]
    #[TestDox('Test that meal creation is rate limited to 15 requests per minute per user')]
    public function test_meal_creation_is_rate_limited_to_30_requests_per_minute(): void
    {
        $cache = Cache::store(config('ratelimit.cache_store'));
        $cache->flush();

        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);

        for ($i = 0; $i < 15; $i++) {
            $response = $this->withToken($token)->postJson("/api/meals/user/{$user->id}", [
                'name' => "Meal $i",
                'calories' => 100,
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->withToken($token)->postJson("/api/meals/user/{$user->id}", [
            'name' => 'Meal 16',
            'calories' => 100,
        ]);
        $this->assertEquals(429, $response->status());
    }
}
