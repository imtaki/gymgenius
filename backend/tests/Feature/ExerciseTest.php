<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;

class ExerciseTest extends TestCase
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
    #[Group('Exercises.Index')]
    #[TestDox('Test that authenticated user can list their exercises')]
    public function test_authenticated_user_can_list_their_exercises(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        Exercise::factory()->count(3)->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson('/api/exercises');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJsonCount(3, 'data');
    }

    #[Test]
    #[Group('Exercises.Index')]
    #[TestDox('Test that user only sees their own exercises')]
    public function test_user_only_sees_their_own_exercises(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        Exercise::factory()->count(2)->for($user1)->create();
        Exercise::factory()->count(3)->for($user2)->create();

        $token = $this->loginAndGetToken($user1);
        $response = $this->withToken($token)->getJson('/api/exercises');

        $response->assertJsonCount(2, 'data');
    }

    #[Test]
    #[Group('Exercises.Index')]
    #[TestDox('Test that unauthenticated user cannot list exercises')]
    public function test_unauthenticated_user_cannot_list_exercises(): void
    {
        $response = $this->getJson('/api/exercises');

        $response->assertUnauthorized();
    }

    // Show tests
    #[Test]
    #[Group('Exercises.Show')]
    #[TestDox('Test that user can view their own exercise')]
    public function test_user_can_view_their_own_exercise(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $exercise = Exercise::factory()->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/exercises/{$exercise->id}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data' => ['id', 'type', 'attributes']]);
        $response->assertJsonPath('data.type', 'exercise');
        $response->assertJsonPath('data.attributes.name', $exercise->name);
        $response->assertJsonPath('data.id', 'ex_' . $exercise->id);
    }

    #[Test]
    #[Group('Exercises.Show')]
    #[TestDox('Test that user cannot view another user\'s exercise')]
    public function test_user_cannot_view_another_users_exercise(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $exercise = Exercise::factory()->for($user1)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->getJson("/api/exercises/{$exercise->id}");

        $response->assertForbidden();
    }

    #[Test]
    #[Group('Exercises.Show')]
    #[TestDox('Test that exercise not found returns 404')]
    public function test_exercise_not_found_returns_404(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson('/api/exercises/99999');

        $response->assertNotFound();
    }

    // Store tests
    #[Test]
    #[Group('Exercises.Store')]
    #[TestDox('Test that authenticated user can create exercise')]
    public function test_authenticated_user_can_create_exercise(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson('/api/exercises', [
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
            'description' => 'Upper body push exercise',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['success', 'data' => ['id', 'type', 'attributes'], 'message']);
        $response->assertJsonPath('data.type', 'exercise');
        $response->assertJsonPath('data.attributes.name', 'Bench Press');
        $this->assertDatabaseHas('exercises', [
            'name' => 'Bench Press',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    #[Group('Exercises.Store')]
    #[TestDox('Test that create exercise validates required fields')]
    public function test_create_exercise_validates_required_fields(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson('/api/exercises', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'muscleGroup']);
    }

    #[Test]
    #[Group('Exercises.Store')]
    #[TestDox('Test that unauthenticated user cannot create exercise')]
    public function test_unauthenticated_user_cannot_create_exercise(): void
    {
        $response = $this->postJson('/api/exercises', [
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
        ]);

        $response->assertUnauthorized();
    }

    // Update tests
    #[Test]
    #[Group('Exercises.Update')]
    #[TestDox('Test that user can update their own exercise')]
    public function test_user_can_update_their_own_exercise(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $exercise = Exercise::factory()->for($user)->create([
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->putJson("/api/exercises/{$exercise->id}", [
            'name' => 'Incline Bench Press',
            'muscleGroup' => 'Upper Chest',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'name' => 'Incline Bench Press',
        ]);
    }

    #[Test]
    #[Group('Exercises.Update')]
    #[TestDox('Test that user cannot update another user\'s exercise')]
    public function test_user_cannot_update_another_users_exercise(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $exercise = Exercise::factory()->for($user1)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->putJson("/api/exercises/{$exercise->id}", [
            'name' => 'Updated Name',
            'muscleGroup' => 'Updated Group',
        ]);

        $response->assertForbidden();
    }

    // Destroy tests
    #[Test]
    #[Group('Exercises.Destroy')]
    #[TestDox('Test that user can delete their own exercise')]
    public function test_user_can_delete_their_own_exercise(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $exercise = Exercise::factory()->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->deleteJson("/api/exercises/{$exercise->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('exercises', ['id' => $exercise->id]);
    }

    #[Test]
    #[Group('Exercises.Destroy')]
    #[TestDox('Test that user cannot delete another user\'s exercise')]
    public function test_user_cannot_delete_another_users_exercise(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $exercise = Exercise::factory()->for($user1)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->deleteJson("/api/exercises/{$exercise->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('exercises', ['id' => $exercise->id]);
    }

    // Muscle groups tests
    #[Test]
    #[Group('Exercises.MuscleGroups')]
    #[TestDox('Test that user can get their unique muscle groups')]
    public function test_user_can_get_their_unique_muscle_groups(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Back']);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson('/api/exercises/muscle-groups');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    #[Group('Exercises.MuscleGroups')]
    #[TestDox('Test that unauthenticated user cannot get muscle groups')]
    public function test_unauthenticated_user_cannot_get_muscle_groups(): void
    {
        $response = $this->getJson('/api/exercises/muscle-groups');

        $response->assertUnauthorized();
    }

    // Rate limiting tests
    #[Test]
    #[Group('Exercises.RateLimit')]
    #[TestDox('Test that create exercise is rate limited to 15 requests per minute per user')]
    public function test_create_exercise_is_rate_limited_to_30_requests_per_minute(): void
    {
        $cache = Cache::store(config('ratelimit.cache_store'));
        $cache->flush();

        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);

        // Make 15 successful requests
        for ($i = 0; $i < 15; $i++) {
            $response = $this->withToken($token)->postJson('/api/exercises', [
                'name' => "Exercise $i",
                'muscleGroup' => 'Chest',
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        // 16th request should be rate limited
        $response = $this->withToken($token)->postJson('/api/exercises', [
            'name' => 'Exercise 16',
            'muscleGroup' => 'Chest',
        ]);
        $this->assertEquals(429, $response->status());
    }
}

