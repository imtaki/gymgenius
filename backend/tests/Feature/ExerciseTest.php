<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExerciseTest extends TestCase
{
    use RefreshDatabase;

    // Index tests
    public function test_authenticated_user_can_list_their_exercises(): void
    {
        $user = User::factory()->create();
        Exercise::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/exercises');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJsonCount(3, 'data');
    }

    public function test_user_only_sees_their_own_exercises(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Exercise::factory()->count(2)->for($user1)->create();
        Exercise::factory()->count(3)->for($user2)->create();

        $response = $this->actingAs($user1)->getJson('/api/exercises');

        $response->assertJsonCount(2, 'data');
    }

    public function test_unauthenticated_user_cannot_list_exercises(): void
    {
        $response = $this->getJson('/api/exercises');

        $response->assertUnauthorized();
    }

    // Show tests
    public function test_user_can_view_their_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson("/api/exercises/{$exercise->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $exercise->id);
        $response->assertJsonPath('data.name', $exercise->name);
    }

    public function test_user_cannot_view_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->getJson("/api/exercises/{$exercise->id}");

        $response->assertForbidden();
    }

    public function test_exercise_not_found_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/exercises/99999');

        $response->assertNotFound();
    }

    // Store tests
    public function test_authenticated_user_can_create_exercise(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/exercises', [
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
            'description' => 'Upper body push exercise',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('exercises', [
            'name' => 'Bench Press',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_exercise_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/exercises', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'muscleGroup']);
    }

    public function test_unauthenticated_user_cannot_create_exercise(): void
    {
        $response = $this->postJson('/api/exercises', [
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
        ]);

        $response->assertUnauthorized();
    }

    // Update tests
    public function test_user_can_update_their_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create([
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
        ]);

        $response = $this->actingAs($user)->putJson("/api/exercises/{$exercise->id}", [
            'name' => 'Incline Bench Press',
            'muscleGroup' => 'Upper Chest',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'name' => 'Incline Bench Press',
        ]);
    }

    public function test_user_cannot_update_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->putJson("/api/exercises/{$exercise->id}", [
            'name' => 'Updated Name',
            'muscleGroup' => 'Updated Group',
        ]);

        $response->assertForbidden();
    }

    // Destroy tests
    public function test_user_can_delete_their_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/exercises/{$exercise->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('exercises', ['id' => $exercise->id]);
    }

    public function test_user_cannot_delete_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->deleteJson("/api/exercises/{$exercise->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('exercises', ['id' => $exercise->id]);
    }

    // Muscle groups tests
    public function test_user_can_get_their_unique_muscle_groups(): void
    {
        $user = User::factory()->create();
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Back']);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);

        $response = $this->actingAs($user)->getJson('/api/exercises/muscle-groups');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_unauthenticated_user_cannot_get_muscle_groups(): void
    {
        $response = $this->getJson('/api/exercises/muscle-groups');

        $response->assertUnauthorized();
    }

    // Rate limiting tests
    public function test_create_exercise_is_rate_limited_to_30_requests_per_minute(): void
    {
        $user = User::factory()->create();

        // Make 30 successful requests
        for ($i = 0; $i < 30; $i++) {
            $response = $this->actingAs($user)->postJson('/api/exercises', [
                'name' => "Exercise $i",
                'muscleGroup' => 'Chest',
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        // 31st request should be rate limited
        $response = $this->actingAs($user)->postJson('/api/exercises', [
            'name' => 'Exercise 31',
            'muscleGroup' => 'Chest',
        ]);
        $this->assertEquals(429, $response->status());
    }
}
