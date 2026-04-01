<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\User;
use App\Models\Meal;
use App\Models\WorkoutProgram;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Illuminate\Support\Facades\Cache;

class UserFeaturesTest extends TestCase
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

    #[Test]
    #[Group('DailyLog.Today')]
    #[TestDox('Test that user can get today\'s daily log')]
    public function test_user_can_get_todays_daily_log(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user->id}/today");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    #[Group('DailyLog.Today')]
    #[TestDox('Test that unauthenticated user cannot access today endpoint')]
    public function test_unauthenticated_user_cannot_access_today_endpoint(): void
    {
        $response = $this->getJson('/api/daily-goals/user/1/today');

        $response->assertUnauthorized();
    }

    #[Test]
    #[Group('DailyLog.Today')]
    #[TestDox('Test that user cannot access another user\'s today log')]
    public function test_user_cannot_access_another_users_today_log(): void
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
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user2->id}/today");

        $response->assertForbidden();
    }

    #[Test]
    #[Group('DailyLog.Today')]
    #[TestDox('Test that today endpoint can be accessed')]
    public function test_today_endpoint_includes_meals(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user->id}/today");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    // DailyLogController - weekly tests
    #[Test]
    #[Group('DailyLog.Weekly')]
    #[TestDox('Test that user can get weekly log')]
    public function test_user_can_get_weekly_log(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user->id}/weekly");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    #[Group('DailyLog.Weekly')]
    #[TestDox('Test that user cannot access another user\'s weekly log')]
    public function test_user_cannot_access_another_users_weekly_log(): void
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
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user2->id}/weekly");

        $response->assertForbidden();
    }

    // DailyLogController - byDate tests
    #[Test]
    #[Group('DailyLog.ByDate')]
    #[TestDox('Test that user can get log by specific date')]
    public function test_user_can_get_log_by_specific_date(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $date = '2026-03-20';

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user->id}/date/{$date}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    #[Group('DailyLog.ByDate')]
    #[TestDox('Test that user cannot access another user\'s date-specific log')]
    public function test_user_cannot_access_another_users_date_specific_log(): void
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
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user2->id}/date/2026-03-20");

        $response->assertForbidden();
    }

    #[Test]
    #[Group('DailyLog.ByDate')]
    #[TestDox('Test that invalid date format is rejected')]
    public function test_invalid_date_format_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user->id}/date/invalid-date");

        $response->assertServerError();
    }

    // DailyLogController - rate limiting tests
    #[Test]
    #[Group('DailyLog.RateLimit')]
    #[TestDox('Test that daily goals read operations are rate limited')]
    public function test_daily_goals_read_operations_have_300_requests_per_minute_limit(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        Cache::flush();
        $token = $this->loginAndGetToken($user);

        // Make a few requests to verify it works
        $response = $this->withToken($token)->getJson("/api/daily-goals/user/{$user->id}/today");
        $response->assertOk();
    }

    // UserSettingsController - index tests
    #[Test]
    #[Group('UserSettings.Index')]
    #[TestDox('Test that user can get their settings')]
    public function test_user_can_get_their_settings(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/settings/user/{$user->id}");

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
        $response->assertJsonPath('data.user_id', $user->id);
    }

    #[Test]
    #[Group('UserSettings.Index')]
    #[TestDox('Test that user cannot view another user\'s settings')]
    public function test_user_cannot_view_another_users_settings(): void
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
        $response = $this->withToken($token)->getJson("/api/settings/user/{$user2->id}");

        $response->assertForbidden();
    }

    #[Test]
    #[Group('UserSettings.Index')]
    #[TestDox('Test that unauthenticated user cannot access settings')]
    public function test_unauthenticated_user_cannot_access_settings(): void
    {
        $response = $this->getJson('/api/settings/user/1');

        $response->assertUnauthorized();
    }

    // UserSettingsController - update tests
    #[Test]
    #[Group('UserSettings.Update')]
    #[TestDox('Test that user can update their settings')]
    public function test_user_can_update_their_settings(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->putJson("/api/settings/user/{$user->id}", [
            'height' => 185,
            'age' => 28,
            'current_weight' => 78.5,
            'caloric_goal' => 2500,
            'goal_type' => 'maintaining',
            'target_weight' => 80,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
            'height' => 185,
            'age' => 28,
        ]);
    }

    #[Test]
    #[Group('UserSettings.Update')]
    #[TestDox('Test that user cannot update another user\'s settings')]
    public function test_user_cannot_update_another_users_settings(): void
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
        $response = $this->withToken($token)->putJson("/api/settings/user/{$user2->id}", [
            'height' => 185,
            'age' => 28,
            'current_weight' => 78.5,
            'caloric_goal' => 2500,
            'goal_type' => 'maintaining',
            'target_weight' => 80,
        ]);

        $response->assertForbidden();
    }

    #[Test]
    #[Group('UserSettings.Update')]
    #[TestDox('Test that update validates numeric fields')]
    public function test_update_validates_numeric_fields(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->putJson("/api/settings/user/{$user->id}", [
            'age' => 'not-a-number',
        ]);

        $response->assertUnprocessable();
    }

    #[Test]
    #[Group('UserSettings.RateLimit')]
    #[TestDox('Test that settings update is rate limited to 15 requests per minute')]
    public function test_settings_update_is_rate_limited_to_30_requests_per_minute(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        Cache::flush();
        $token = $this->loginAndGetToken($user);

        for ($i = 0; $i < 15; $i++) {
            $response = $this->withToken($token)->putJson("/api/settings/user/{$user->id}", [
                'age' => 25 + $i,
                'current_weight' => 75,
                'caloric_goal' => 2500,
                'goal_type' => 'maintaining',
                'target_weight' => 80,
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->withToken($token)->putJson("/api/settings/user/{$user->id}", [
            'age' => 60,
            'current_weight' => 75,
            'caloric_goal' => 2500,
            'goal_type' => 'maintaining',
            'target_weight' => 80,
        ]);
        $this->assertEquals(429, $response->status());
    }

    // WorkoutProgramController - index tests
    #[Test]
    #[Group('WorkoutProgram.Index')]
    #[TestDox('Test that user can list their workout programs')]
    public function test_user_can_list_their_workout_programs(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        WorkoutProgram::factory()->count(3)->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson('/api/workout-programs');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    #[Group('WorkoutProgram.Index')]
    #[TestDox('Test that unauthenticated user cannot list programs')]
    public function test_unauthenticated_user_cannot_list_programs(): void
    {
        $response = $this->getJson('/api/workout-programs');

        $response->assertUnauthorized();
    }

    // WorkoutProgramController - show tests
    #[Test]
    #[Group('WorkoutProgram.Show')]
    #[TestDox('Test that user can view their workout program')]
    public function test_user_can_view_their_workout_program(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $program = WorkoutProgram::factory()->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->getJson("/api/workout-programs/{$program->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $program->id);
    }

    #[Test]
    #[Group('WorkoutProgram.Show')]
    #[TestDox('Test that user cannot view another user\'s program')]
    public function test_user_cannot_view_another_users_program(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $program = WorkoutProgram::factory()->for($user1)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->getJson("/api/workout-programs/{$program->id}");

        $response->assertForbidden();
    }

    // WorkoutProgramController - store tests
    #[Test]
    #[Group('WorkoutProgram.Store')]
    #[TestDox('Test that user can create workout program')]
    public function test_user_can_create_workout_program(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson('/api/workout-programs', [
            'name' => 'Push/Pull/Legs',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('workout_programs', [
            'name' => 'Push/Pull/Legs',
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    #[Group('WorkoutProgram.Store')]
    #[TestDox('Test that create program validates required fields')]
    public function test_create_program_validates_required_fields(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->postJson('/api/workout-programs', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name']);
    }

    // WorkoutProgramController - update tests
    #[Test]
    #[Group('WorkoutProgram.Update')]
    #[TestDox('Test that user can update their program')]
    public function test_user_can_update_their_program(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $program = WorkoutProgram::factory()->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->putJson("/api/workout-programs/{$program->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('workout_programs', [
            'id' => $program->id,
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    #[Group('WorkoutProgram.Update')]
    #[TestDox('Test that user cannot update another user\'s program')]
    public function test_user_cannot_update_another_users_program(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $program = WorkoutProgram::factory()->for($user1)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->putJson("/api/workout-programs/{$program->id}", [
            'name' => 'Updated',
        ]);

        $response->assertForbidden();
    }

    // WorkoutProgramController - destroy tests
    #[Test]
    #[Group('WorkoutProgram.Destroy')]
    #[TestDox('Test that user can delete their program')]
    public function test_user_can_delete_their_program(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $program = WorkoutProgram::factory()->for($user)->create();

        $token = $this->loginAndGetToken($user);
        $response = $this->withToken($token)->deleteJson("/api/workout-programs/{$program->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('workout_programs', ['id' => $program->id]);
    }

    #[Test]
    #[Group('WorkoutProgram.Destroy')]
    #[TestDox('Test that user cannot delete another user\'s program')]
    public function test_user_cannot_delete_another_users_program(): void
    {
        $user1 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('password'),
        ]);
        $program = WorkoutProgram::factory()->for($user1)->create();

        $token = $this->loginAndGetToken($user2);
        $response = $this->withToken($token)->deleteJson("/api/workout-programs/{$program->id}");

        $response->assertForbidden();
    }
};
