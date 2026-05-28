<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

class LoggedSetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[Group('logged-set')]
    public function test_cannot_log_set_for_exercise_not_in_split(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->actingAs($user, 'api');

        // Create split directly
        $splitModel = \App\Models\WorkoutSplit::create([
            'user_id' => $user->id,
            'name' => 'Accessory',
        ]);
        $split = ['id' => $splitModel->id];

        $validEx = Exercise::factory()->create(['user_id' => $user->id]);
        $this->withHeader('Authorization', "Bearer $token")->postJson("/api/workout-splits/{$split['id']}/exercises", [
            'exercise_id' => $validEx->id,
            'order' => 1,
        ])->assertStatus(201);

        // Start workout
        $workout = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/workouts', [
            'workout_split_id' => $split['id'],
            'date' => now()->toDateString(),
        ])->json('data');

        // Create some other exercise not in split
        $otherEx = Exercise::factory()->create();

        // Attempt to log set with exercise id not linked to split
        $res = $this->withHeader('Authorization', "Bearer $token")->postJson("/api/workouts/{$workout['id']}/sets", [
            'workout_split_exercise_id' => 999999, // non-existent or not belonging to split
            'set_number' => 1,
            'reps' => 8,
        ]);

        $res->assertStatus(422);
    }
}
