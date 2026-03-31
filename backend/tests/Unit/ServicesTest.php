<?php

namespace Tests\Unit;

use App\Models\DailyLog;
use App\Models\Exercise;
use App\Models\Meal;
use App\Models\User;
use App\Services\ExerciseService;
use App\Services\MealService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    // ExerciseService::getExercisesByUser tests
    public function test_returns_all_exercises_for_a_user(): void
    {
        $user = User::factory()->create();
        Exercise::factory()->count(3)->for($user)->create();

        $service = app(ExerciseService::class);
        $exercises = $service->getExercisesByUser($user->id);

        $this->assertCount(3, $exercises);
    }

    public function test_returns_empty_collection_for_user_with_no_exercises(): void
    {
        $user = User::factory()->create();

        $service = app(ExerciseService::class);
        $exercises = $service->getExercisesByUser($user->id);

        $this->assertCount(0, $exercises);
    }

    public function test_caches_results_for_user_exercises(): void
    {
        $user = User::factory()->create();
        Exercise::factory()->count(2)->for($user)->create();

        $service = app(ExerciseService::class);

        // First call - hits database
        $exercises1 = $service->getExercisesByUser($user->id);
        $this->assertCount(2, $exercises1);

        // Create new exercise after service call
        Exercise::factory()->for($user)->create();

        // Second call should return cached result (still 2)
        $exercises2 = $service->getExercisesByUser($user->id);
        $this->assertCount(2, $exercises2);
    }

    public function test_only_returns_exercises_for_specified_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Exercise::factory()->count(2)->for($user1)->create();
        Exercise::factory()->count(3)->for($user2)->create();

        $service = app(ExerciseService::class);
        $exercises = $service->getExercisesByUser($user1->id);

        $this->assertCount(2, $exercises);
        $exercises->each(fn($e) => $this->assertEquals($user1->id, $e->user_id));
    }

    // ExerciseService::getExerciseById tests
    public function test_returns_exercise_by_id(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        $service = app(ExerciseService::class);
        $found = $service->getExerciseById($exercise->id);

        $this->assertTrue($found->is($exercise));
    }

    public function test_returns_null_for_non_existent_exercise(): void
    {
        $service = app(ExerciseService::class);
        $found = $service->getExerciseById(99999);

        $this->assertNull($found);
    }

    public function test_caches_individual_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create(['name' => 'Original']);

        $service = app(ExerciseService::class);

        // First call
        $found1 = $service->getExerciseById($exercise->id);
        $this->assertEquals('Original', $found1->name);

        // Update exercise in database
        $exercise->update(['name' => 'Updated']);

        // Second call should return cached result
        $found2 = $service->getExerciseById($exercise->id);
        $this->assertEquals('Original', $found2->name);
    }

    // ExerciseService::createExercise tests
    public function test_creates_exercise_with_valid_data(): void
    {
        $user = User::factory()->create();

        $service = app(ExerciseService::class);
        $exercise = $service->createExercise($user->id, [
            'name' => 'Bench Press',
            'muscleGroup' => 'Chest',
            'description' => 'Upper body push',
        ]);

        $this->assertNotNull($exercise->id);
        $this->assertEquals('Bench Press', $exercise->name);
        $this->assertEquals($user->id, $exercise->user_id);
    }

    public function test_invalidates_user_cache_when_creating(): void
    {
        $user = User::factory()->create();
        $cacheKey = "user_{$user->id}_exercises";

        // Set cache
        Cache::put($cacheKey, [], 3600);
        $this->assertTrue(Cache::has($cacheKey));

        $service = app(ExerciseService::class);
        $service->createExercise($user->id, [
            'name' => 'Exercise',
            'muscleGroup' => 'Back',
        ]);

        // Cache should be cleared
        $this->assertFalse(Cache::has($cacheKey));
    }

    // ExerciseService::updateExercise tests
    public function test_updates_exercise_with_new_data(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create(['name' => 'Old Name']);

        $service = app(ExerciseService::class);
        $updated = $service->updateExercise($exercise->id, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('exercises', ['id' => $exercise->id, 'name' => 'New Name']);
    }

    public function test_invalidates_caches_on_update(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        Cache::put("exercise_{$exercise->id}", $exercise, 3600);
        Cache::put("user_{$user->id}_exercises", [$exercise], 3600);

        $service = app(ExerciseService::class);
        $service->updateExercise($exercise->id, ['name' => 'Updated']);

        $this->assertFalse(Cache::has("exercise_{$exercise->id}"));
        $this->assertFalse(Cache::has("user_{$user->id}_exercises"));
    }

    // ExerciseService::deleteExercise tests
    public function test_deletes_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();
        $exerciseId = $exercise->id;

        $service = app(ExerciseService::class);
        $service->deleteExercise($exerciseId);

        $this->assertDatabaseMissing('exercises', ['id' => $exerciseId]);
    }

    public function test_invalidates_caches_on_delete(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->for($user)->create();

        Cache::put("exercise_{$exercise->id}", $exercise, 3600);
        Cache::put("user_{$user->id}_exercises", [$exercise], 3600);

        $service = app(ExerciseService::class);
        $service->deleteExercise($exercise->id);

        $this->assertFalse(Cache::has("exercise_{$exercise->id}"));
        $this->assertFalse(Cache::has("user_{$user->id}_exercises"));
    }

    // ExerciseService::getMuscleGroupsByUser tests
    public function test_returns_unique_muscle_groups_for_user(): void
    {
        $user = User::factory()->create();
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Back']);
        Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);

        $service = app(ExerciseService::class);
        $groups = $service->getMuscleGroupsByUser($user->id);

        $unique = $groups->unique()->count();
        $this->assertEquals(2, $unique);
    }

    public function test_returns_empty_collection_for_user_with_no_exercises_muscle_groups(): void
    {
        $user = User::factory()->create();

        $service = app(ExerciseService::class);
        $groups = $service->getMuscleGroupsByUser($user->id);

        $this->assertCount(0, $groups);
    }

    // MealService::getMealByUser tests
    public function test_returns_meals_for_user(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        Meal::factory()->count(3)->for($user)->for($dailyLog)->create();

        $service = app(MealService::class);
        $meals = $service->getMealByUser($user->id);

        $this->assertCount(3, $meals);
    }

    public function test_caches_user_meals(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        Meal::factory()->count(2)->for($user)->for($dailyLog)->create();

        $service = app(MealService::class);

        // First call
        $meals1 = $service->getMealByUser($user->id);
        $this->assertCount(2, $meals1);

        // Create another meal
        Meal::factory()->for($user)->for($dailyLog)->create();

        // Second call returns cached result
        $meals2 = $service->getMealByUser($user->id);
        $this->assertCount(2, $meals2);
    }

    // MealService::getMealById tests
    public function test_returns_meal_by_id(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        $service = app(MealService::class);
        $found = $service->getMealById($meal->id);

        $this->assertTrue($found->is($meal));
    }

    public function test_caches_individual_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create(['name' => 'Original']);

        $service = app(MealService::class);

        // First call
        $found1 = $service->getMealById($meal->id);
        $this->assertEquals('Original', $found1->name);

        // Update meal
        $meal->update(['name' => 'Updated']);

        // Second call returns cached
        $found2 = $service->getMealById($meal->id);
        $this->assertEquals('Original', $found2->name);
    }

    // MealService::createMeal tests
    public function test_creates_meal_with_auto_daily_log_association(): void
    {
        $user = User::factory()->create();

        $service = app(MealService::class);
        $meal = $service->createMeal($user->id, [
            'name' => 'Chicken',
            'calories' => 165,
            'protein' => 31,
        ]);

        $this->assertNotNull($meal->id);
        $this->assertEquals('Chicken', $meal->name);
        $this->assertNotNull($meal->daily_log_id);
    }

    public function test_invalidates_caches_on_meal_creation(): void
    {
        $user = User::factory()->create();
        $cacheKey = "user_{$user->id}_meals";

        Cache::put($cacheKey, [], 3600);
        $this->assertTrue(Cache::has($cacheKey));

        $service = app(MealService::class);
        $service->createMeal($user->id, [
            'name' => 'Meal',
            'calories' => 100,
        ]);

        $this->assertFalse(Cache::has($cacheKey));
    }

    // MealService::updateMeal tests
    public function test_updates_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create(['calories' => 100]);

        $service = app(MealService::class);
        $updated = $service->updateMeal($meal->id, ['calories' => 150]);

        $this->assertEquals(150, $updated->calories);
    }

    // MealService::deleteMeal tests
    public function test_deletes_meal(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();
        $mealId = $meal->id;

        $service = app(MealService::class);
        $service->deleteMeal($mealId);

        $this->assertDatabaseMissing('meals', ['id' => $mealId]);
    }
}
