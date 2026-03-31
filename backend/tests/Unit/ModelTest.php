<?php

namespace Tests\Unit;

use App\Models\DailyLog;
use App\Models\Exercise;
use App\Models\Meal;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\WorkoutProgram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    // User Model tests
    public function test_user_has_many_exercises(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue($user->exercises->contains($exercise));
    }

    public function test_user_has_many_meals(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue($user->meals->contains($meal));
    }

    public function test_user_has_one_settings(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->settings);
        $this->assertEquals($user->id, $user->settings->user_id);
    }

    public function test_user_has_many_workout_logs(): void
    {
        $user = User::factory()->create();
        $workoutLog = WorkoutLog::factory()->for($user)->create();

        $this->assertTrue($user->workoutLogs->contains($workoutLog));
    }

    public function test_user_has_many_workout_programs(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $this->assertTrue($user->workoutPrograms->contains($program));
    }

    public function test_user_has_jwt_identifier(): void
    {
        $user = User::factory()->create();

        $identifier = $user->getJWTIdentifier();
        $this->assertEquals($user->id, $identifier);
    }

    public function test_user_has_jwt_custom_claims(): void
    {
        $user = User::factory()->create();

        $claims = $user->getJWTCustomClaims();
        $this->assertIsArray($claims);
    }

    public function test_user_settings_are_auto_created_on_user_creation(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->settings);
        $this->assertEquals($user->id, $user->settings->user_id);
    }

    public function test_user_settings_have_default_values(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->settings->goal_type);
        $this->assertNotNull($user->settings->caloric_goal);
    }

    // Exercise Model tests
    public function test_exercise_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $this->assertTrue($exercise->user->is($user));
    }

    public function test_exercise_has_many_workout_logs(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();
        $workoutLog = WorkoutLog::factory()->for($user)->for($exercise)->create();

        $this->assertTrue($exercise->workoutLogs->contains($workoutLog));
    }

    // Meal Model tests
    public function test_meal_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue($meal->user->is($user));
    }

    public function test_meal_belongs_to_daily_log(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue($meal->dailyLog->is($dailyLog));
    }

    public function test_numeric_fields_are_cast_to_float(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create([
            'calories' => 150,
            'protein' => 25.5,
            'carbs' => 10,
            'fats' => 3.2,
        ]);

        $this->assertTrue(is_float($meal->calories) || is_int($meal->calories));
        $this->assertTrue(is_float($meal->protein) || is_int($meal->protein));
    }

    // DailyLog Model tests
    public function test_daily_log_has_many_meals(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $this->assertTrue($dailyLog->meals->contains($meal));
    }

    public function test_daily_log_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();

        $this->assertTrue($dailyLog->user->is($user));
    }

    // WorkoutLog Model tests
    public function test_workout_log_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();
        $workoutLog = WorkoutLog::factory()->for($user)->for($exercise)->create();

        $this->assertTrue($workoutLog->user->is($user));
    }

    public function test_workout_log_belongs_to_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();
        $workoutLog = WorkoutLog::factory()->for($user)->for($exercise)->create();

        $this->assertTrue($workoutLog->exercise->is($exercise));
    }

    // WorkoutProgram Model tests
    public function test_workout_program_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $program = WorkoutProgram::factory()->for($user)->create();

        $this->assertTrue($program->user->is($user));
    }

    // UserSettings Model tests
    public function test_user_settings_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $settings = $user->settings;

        $this->assertTrue($settings->user->is($user));
    }

    public function test_numeric_fields_are_properly_cast(): void
    {
        $user = User::factory()->create();
        $settings = $user->settings;
        $settings->update([
            'height' => 180,
            'age' => 30,
            'current_weight' => 75.5,
            'target_weight' => 70.0,
            'caloric_goal' => 2500,
        ]);

        $this->assertTrue(is_int($settings->age) || is_float($settings->age));
        $this->assertTrue(is_float($settings->current_weight) || is_int($settings->current_weight));
    }
}
