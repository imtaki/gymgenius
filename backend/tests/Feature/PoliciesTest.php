<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\Meal;
use App\Models\User;
use App\Models\DailyLog;
use App\Models\WorkoutProgram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    use RefreshDatabase;

    // ExercisePolicy tests
    public function test_user_can_view_their_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $exercise));
    }

    public function test_user_can_view_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        // Different users can view each other's exercises
        $this->assertTrue(Gate::forUser($user2)->allows('view', $exercise));
    }

    public function test_any_authenticated_user_can_create_exercise(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('create', Exercise::class));
    }

    public function test_user_can_update_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $exercise));
    }

    public function test_user_cannot_update_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('update', $exercise));
    }

    public function test_user_can_delete_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('delete', $exercise));
    }

    public function test_user_cannot_delete_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('delete', $exercise));
    }

    // MealPolicy tests
    public function test_user_can_view_their_own_meals(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        Meal::factory()->for($user)->for($dailyLog)->create();

        // MealPolicy requires user->id === userId parameter
        $this->assertTrue(
            Gate::forUser($user)->allows('viewAny', [Meal::class, $user->id])
        );
    }

    public function test_user_cannot_view_another_users_meals(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(
            Gate::forUser($user1)->allows('viewAny', [Meal::class, $user2->id])
        );
    }

    public function test_user_can_view_their_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $meal));
    }

    public function test_user_cannot_view_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('view', $meal));
    }

    public function test_user_can_create_meal_for_themselves(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(
            Gate::forUser($user)->allows('create', [Meal::class, $user->id])
        );
    }

    public function test_user_cannot_create_meal_for_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(
            Gate::forUser($user1)->allows('create', [Meal::class, $user2->id])
        );
    }

    public function test_user_can_update_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $meal));
    }

    public function test_user_cannot_update_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('update', $meal));
    }

    public function test_user_can_delete_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue(Gate::forUser($user)->allows('delete', $meal));
    }

    public function test_user_cannot_delete_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('delete', $meal));
    }

    // DailyLogPolicy tests
    public function test_user_can_view_their_own_daily_logs(): void
    {
        $user = User::factory()->create();
        DailyLog::factory()->for($user)->create();

        $this->assertTrue(
            Gate::forUser($user)->allows('viewAny', [DailyLog::class, $user->id])
        );
    }

    public function test_user_cannot_view_another_users_daily_logs(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(
            Gate::forUser($user1)->allows('viewAny', [DailyLog::class, $user2->id])
        );
    }

    // UserSettingsPolicy tests
    public function test_user_can_view_their_own_settings(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $user->settings));
    }

    public function test_user_cannot_view_another_users_settings(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(Gate::forUser($user1)->allows('view', $user2->settings));
    }

    public function test_user_can_update_their_own_settings(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $user->settings));
    }

    public function test_user_cannot_update_another_users_settings(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(Gate::forUser($user1)->allows('update', $user2->settings));
    }

    // WorkoutProgramPolicy tests
    public function test_user_can_view_their_own_workout_program(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $program));
    }

    public function test_user_cannot_view_another_users_workout_program(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('view', $program));
    }

    public function test_user_can_update_their_own_workout_program(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $program));
    }

    public function test_user_cannot_update_another_users_workout_program(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('update', $program));
    }

    public function test_user_can_delete_their_own_workout_program(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('delete', $program));
    }

    public function test_user_cannot_delete_another_users_workout_program(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('delete', $program));
    }
}
