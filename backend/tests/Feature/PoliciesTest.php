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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

class PoliciesTest extends TestCase
{
    use RefreshDatabase;

    // ExercisePolicy tests
    #[Test]
    #[Group('ExercisePolicy.View')]
    #[TestDox('Test that user can view their own exercise')]
    public function test_user_can_view_their_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $exercise));
    }

    #[Test]
    #[Group('ExercisePolicy.View')]
    #[TestDox('Test that user can view another user\'s exercise')]
    public function test_user_can_view_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        // Different users can view each other's exercises
        $this->assertTrue(Gate::forUser($user2)->allows('view', $exercise));
    }

    #[Test]
    #[Group('ExercisePolicy.Create')]
    #[TestDox('Test that any authenticated user can create exercise')]
    public function test_any_authenticated_user_can_create_exercise(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('create', Exercise::class));
    }

    #[Test]
    #[Group('ExercisePolicy.Update')]
    #[TestDox('Test that user can update own exercise')]
    public function test_user_can_update_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $exercise));
    }

    #[Test]
    #[Group('ExercisePolicy.Update')]
    #[TestDox('Test that user cannot update another user\'s exercise')]
    public function test_user_cannot_update_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('update', $exercise));
    }

    #[Test]
    #[Group('ExercisePolicy.Delete')]
    #[TestDox('Test that user can delete own exercise')]
    public function test_user_can_delete_own_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue(Gate::forUser($user)->allows('delete', $exercise));
    }

    #[Test]
    #[Group('ExercisePolicy.Delete')]
    #[TestDox('Test that user cannot delete another user\'s exercise')]
    public function test_user_cannot_delete_another_users_exercise(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $exercise = Exercise::factory()->for($user1)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('delete', $exercise));
    }

    // MealPolicy tests
    #[Test]
    #[Group('MealPolicy.ViewAny')]
    #[TestDox('Test that user can view their own meals')]
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

    #[Test]
    #[Group('MealPolicy.ViewAny')]
    #[TestDox('Test that user cannot view another user\'s meals')]
    public function test_user_cannot_view_another_users_meals(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(
            Gate::forUser($user1)->allows('viewAny', [Meal::class, $user2->id])
        );
    }

    #[Test]
    #[Group('MealPolicy.View')]
    #[TestDox('Test that user can view their own meal')]
    public function test_user_can_view_their_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $meal));
    }

    #[Test]
    #[Group('MealPolicy.View')]
    #[TestDox('Test that user cannot view another user\'s meal')]
    public function test_user_cannot_view_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('view', $meal));
    }

    #[Test]
    #[Group('MealPolicy.Create')]
    #[TestDox('Test that user can create meal for themselves')]
    public function test_user_can_create_meal_for_themselves(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(
            Gate::forUser($user)->allows('create', [Meal::class, $user->id])
        );
    }

    #[Test]
    #[Group('MealPolicy.Create')]
    #[TestDox('Test that user cannot create meal for another user')]
    public function test_user_cannot_create_meal_for_another_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(
            Gate::forUser($user1)->allows('create', [Meal::class, $user2->id])
        );
    }

    #[Test]
    #[Group('MealPolicy.Update')]
    #[TestDox('Test that user can update own meal')]
    public function test_user_can_update_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue(Gate::forUser($user)->allows('update', $meal));
    }

    #[Test]
    #[Group('MealPolicy.Update')]
    #[TestDox('Test that user cannot update another user\'s meal')]
    public function test_user_cannot_update_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('update', $meal));
    }

    #[Test]
    #[Group('MealPolicy.Delete')]
    #[TestDox('Test that user can delete own meal')]
    public function test_user_can_delete_own_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue(Gate::forUser($user)->allows('delete', $meal));
    }

    #[Test]
    #[Group('MealPolicy.Delete')]
    #[TestDox('Test that user cannot delete another user\'s meal')]
    public function test_user_cannot_delete_another_users_meal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user1)->create();
        $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

        $this->assertFalse(Gate::forUser($user2)->allows('delete', $meal));
    }

    // DailyLogPolicy tests
    #[Test]
    #[Group('DailyLogPolicy.ViewAny')]
    #[TestDox('Test that user can view their own daily logs')]
    public function test_user_can_view_their_own_daily_logs(): void
    {
        $user = User::factory()->create();
        DailyLog::factory()->for($user)->create();

        $this->assertTrue(
            Gate::forUser($user)->allows('viewAny', [DailyLog::class, $user->id])
        );
    }

    #[Test]
    #[Group('DailyLogPolicy.ViewAny')]
    #[TestDox('Test that user cannot view another user\'s daily logs')]
    public function test_user_cannot_view_another_users_daily_logs(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->assertFalse(
            Gate::forUser($user1)->allows('viewAny', [DailyLog::class, $user2->id])
        );
    }

    // UserSettingsPolicy tests
    #[Test]
    #[Group('UserSettingsPolicy.View')]
    #[TestDox('Test that user can view their own settings')]
    public function test_user_can_view_their_own_settings(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(Gate::forUser($user)->allows('view', $user->settings));
    }

    #[Test]
    #[Group('UserSettingsPolicy.View')]
    #[TestDox('Test that user cannot view another user\'s settings')]
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
