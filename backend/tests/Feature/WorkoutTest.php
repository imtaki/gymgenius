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

class WorkoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    #[Group('workout')]
    public function test_start_workout_log_sets_and_end(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->actingAs($user, 'api');

        // Create split directly
        $splitModel = WorkoutSplit::create([
            'user_id' => $user->id,
            'name' => 'Full Body',
        ]);

        $splitId = $splitModel->id;

        $ex = Exercise::factory()->create(['user_id' => $user->id]);
        $this->withHeader('Authorization', "Bearer $token")->postJson("/api/workout-splits/{$splitId}/exercises", [
            'exercise_id' => $ex->id,
            'order' => 1,
        ])->assertStatus(201);

        // Start workout
        $workout = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/workouts', [
            'workout_split_id' => $splitId,
            'date' => now()->toDateString(),
            'notes' => 'Felt strong',
        ])->assertStatus(201)->json('data');

        $this->assertEquals($splitId, $workout['workout_split_id']);

        // Log set
        $set = $this->withHeader('Authorization', "Bearer $token")->postJson("/api/workouts/{$workout['id']}/sets", [
            'workout_split_exercise_id' => $workout['exercises'][0]['id'] ?? 0,
            'set_number' => 1,
            'reps' => 5,
            'weight' => 100.5,
            'rpe' => 7,
        ])->assertStatus(201)->json('data');

        $this->assertDatabaseHas('logged_sets', ['id' => $set['id'], 'reps' => 5]);

        // End workout
        $endedAt = now()->toIso8601String();
        $updated = $this->withHeader('Authorization', "Bearer $token")->putJson("/api/workouts/{$workout['id']}", [
            'ended_at' => $endedAt,
            'notes' => 'Great session',
        ])->assertOk()->json('data');

        $this->assertNotNull($updated['ended_at']);
    }
}
