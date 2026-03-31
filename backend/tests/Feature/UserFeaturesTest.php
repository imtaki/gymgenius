<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\User;
use App\Models\Meal;
use App\Models\WorkoutProgram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFeaturesTest extends TestCase
{
    use RefreshDatabase;

    // DailyLogController - today tests
    public function test_user_can_get_todays_daily_log(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_unauthenticated_user_cannot_access_today_endpoint(): void
    {
        $response = $this->getJson('/api/daily-goals/user/1/today');

        $response->assertUnauthorized();
    }

    public function test_user_cannot_access_another_users_today_log(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->getJson("/api/daily-goals/user/{$user2->id}/today");

        $response->assertForbidden();
    }

    public function test_today_endpoint_includes_meals(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create(['date' => now()->format('Y-m-d')]);
        Meal::factory()->count(2)->for($user)->for($dailyLog)->create();

        $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data' => ['meals']]);
    }

    // DailyLogController - weekly tests
    public function test_user_can_get_weekly_log(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/weekly");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_user_cannot_access_another_users_weekly_log(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->getJson("/api/daily-goals/user/{$user2->id}/weekly");

        $response->assertForbidden();
    }

    // DailyLogController - byDate tests
    public function test_user_can_get_log_by_specific_date(): void
    {
        $user = User::factory()->create();
        $date = '2026-03-20';

        $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/date/{$date}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_user_cannot_access_another_users_date_specific_log(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->getJson("/api/daily-goals/user/{$user2->id}/date/2026-03-20");

        $response->assertForbidden();
    }

    public function test_invalid_date_format_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/date/invalid-date");

        $response->assertNotFound();
    }

    // DailyLogController - rate limiting tests
    public function test_daily_goals_read_operations_have_300_requests_per_minute_limit(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 300; $i++) {
            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");
        $this->assertEquals(429, $response->status());
    }

    // UserSettingsController - index tests
    public function test_user_can_get_their_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/settings/user/{$user->id}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJsonPath('data.user_id', $user->id);
    }

    public function test_user_cannot_view_another_users_settings(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->getJson("/api/settings/user/{$user2->id}");

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_access_settings(): void
    {
        $response = $this->getJson('/api/settings/user/1');

        $response->assertUnauthorized();
    }

    // UserSettingsController - update tests
    public function test_user_can_update_their_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
            'height' => 185,
            'age' => 28,
            'current_weight' => 78.5,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
            'height' => 185,
            'age' => 28,
        ]);
    }

    public function test_user_cannot_update_another_users_settings(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->putJson("/api/settings/user/{$user2->id}", [
            'height' => 185,
        ]);

        $response->assertForbidden();
    }

    public function test_update_validates_numeric_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
            'age' => 'not-a-number',
        ]);

        $response->assertUnprocessable();
    }

    // UserSettingsController - rate limiting tests
    public function test_settings_update_is_rate_limited_to_30_requests_per_minute(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 30; $i++) {
            $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
                'age' => 25 + $i,
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
            'age' => 60,
        ]);
        $this->assertEquals(429, $response->status());
    }

    // WorkoutProgramController - index tests
    public function test_user_can_list_their_workout_programs(): void
    {
        $user = User::factory()->create();
        WorkoutProgram::factory()->count(3)->for($user)->create();

        $response = $this->actingAs($user)->getJson('/api/workout-programs');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_unauthenticated_user_cannot_list_programs(): void
    {
        $response = $this->getJson('/api/workout-programs');

        $response->assertUnauthorized();
    }

    // WorkoutProgramController - show tests
    public function test_user_can_view_their_workout_program(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $response = $this->actingAs($user)->getJson("/api/workout-programs/{$program->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $program->id);
    }

    public function test_user_cannot_view_another_users_program(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->getJson("/api/workout-programs/{$program->id}");

        $response->assertForbidden();
    }

    // WorkoutProgramController - store tests
    public function test_user_can_create_workout_program(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/workout-programs', [
            'name' => 'Push/Pull/Legs',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('workout_programs', [
            'name' => 'Push/Pull/Legs',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_program_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/workout-programs', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    // WorkoutProgramController - update tests
    public function test_user_can_update_their_program(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $response = $this->actingAs($user)->putJson("/api/workout-programs/{$program->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('workout_programs', [
            'id' => $program->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_cannot_update_another_users_program(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->putJson("/api/workout-programs/{$program->id}", [
            'name' => 'Updated',
        ]);

        $response->assertForbidden();
    }

    // WorkoutProgramController - destroy tests
    public function test_user_can_delete_their_program(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/workout-programs/{$program->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('workout_programs', ['id' => $program->id]);
    }

    public function test_user_cannot_delete_another_users_program(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user1)->create();

        $response = $this->actingAs($user2)->deleteJson("/api/workout-programs/{$program->id}");

        $response->assertForbidden();
    }
};
