<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[Group('auth')]
    public function test_user_cannot_access_another_users_split_or_workout(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tokenA = JWTAuth::fromUser($userA);
        $tokenB = JWTAuth::fromUser($userB);

        // User A creates split
        $this->actingAs($userA, 'api');
        $splitModel = \App\Models\WorkoutSplit::create([
            'user_id' => $userA->id,
            'name' => 'Private Split',
        ]);
        $split = ['id' => $splitModel->id];

        // User B cannot view
        $this->actingAs($userB, 'api');
        $this->withHeader('Authorization', "Bearer $tokenB")->getJson("/api/workout-splits/{$split['id']}")->assertStatus(403);

        // User A starts workout
        $this->actingAs($userA, 'api');
        $workout = $this->withHeader('Authorization', "Bearer $tokenA")->postJson('/api/workouts', [
            'workout_split_id' => $split['id'],
            'date' => now()->toDateString(),
        ])->json('data');

        // User B cannot view workout
        $this->actingAs($userB, 'api');
        $this->withHeader('Authorization', "Bearer $tokenB")->getJson("/api/workouts/{$workout['id']}")->assertStatus(403);

        // User B cannot log sets to User A's workout
        $this->actingAs($userB, 'api');
        $this->withHeader('Authorization', "Bearer $tokenB")->postJson("/api/workouts/{$workout['id']}/sets", [
            'workout_split_exercise_id' => 1,
            'set_number' => 1,
            'reps' => 5,
        ])->assertStatus(403);
    }
}
