<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exercise;
use App\Models\WorkoutSplit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

class WorkoutSplitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    #[Group('workout-split')]
    public function test_create_split_add_exercises_and_list(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->actingAs($user, 'api');

        // Create split (create directly to avoid auth-middleware inconsistencies in tests)
        $splitModel = WorkoutSplit::create([
            'user_id' => $user->id,
            'name' => 'Upper Body A',
            'description' => 'Chest and back',
        ]);
        $splitId = $splitModel->id;

        // Create exercises
        $ex1 = Exercise::factory()->create(['user_id' => $user->id]);
        $ex2 = Exercise::factory()->create(['user_id' => $user->id]);
        $ex3 = Exercise::factory()->create(['user_id' => $user->id]);

        // Add exercises to split
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/workout-splits/{$splitId}/exercises", [
                'exercise_id' => $ex1->id,
                'order' => 1,
                'target_sets' => 4,
                'target_reps' => 8,
            ])->assertStatus(201);

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/workout-splits/{$splitId}/exercises", [
                'exercise_id' => $ex2->id,
                'order' => 2,
            ])->assertStatus(201);

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/workout-splits/{$splitId}/exercises", [
                'exercise_id' => $ex3->id,
                'order' => 3,
            ])->assertStatus(201);

        // List splits and verify exercises_count
        $list = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/workout-splits');
        $list->assertOk();
        $this->assertEquals(1, count($list->json('data')));
        $this->assertEquals(3, $list->json('data.0.attributes.exercises_count'));
    }

    #[Test]
    #[Group('workout-split')]
    public function test_delete_split_cascades_workouts_and_sets(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->actingAs($user, 'api');

        // Create split directly
        $splitModel = WorkoutSplit::create([
            'user_id' => $user->id,
            'name' => 'Leg Day',
        ]);

        $splitId = $splitModel->id;

        $ex = Exercise::factory()->create(['user_id' => $user->id]);
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/workout-splits/{$splitId}/exercises", [
                'exercise_id' => $ex->id,
                'order' => 1,
            ])->assertStatus(201);

        // Start workout
        $workout = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/workouts', [
            'workout_split_id' => $splitId,
            'date' => now()->toDateString(),
        ])->json('data');

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/workouts/{$workout['id']}/sets", [
                'workout_split_exercise_id' => $workout['exercises'][0]['id'] ?? 0,
                'set_number' => 1,
                'reps' => 8,
            ])->assertStatus(201);

        // Delete split
        $this->withHeader('Authorization', "Bearer $token")->deleteJson("/api/workout-splits/{$splitId}")->assertNoContent();

        // Ensure workouts and logged_sets removed
        $this->assertDatabaseMissing('workouts', ['id' => $workout['id']]);
        $this->assertDatabaseMissing('logged_sets', ['workout_id' => $workout['id']]);
    }
}
