<?php

use App\Models\User;
use App\Models\Exercise;
use App\Models\Meal;
use App\Models\DailyLog;
use App\Services\ExerciseService;
use App\Services\MealService;
use Illuminate\Support\Facades\Cache;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('ExerciseService', function () {
    describe('getExercisesByUser', function () {
        test('returns all exercises for a user', function () {
            $user = User::factory()->create();
            Exercise::factory()->count(3)->for($user)->create();

            $service = app(ExerciseService::class);
            $exercises = $service->getExercisesByUser($user->id);

            $this->assertCount(3, $exercises);
        });

        test('returns empty collection for user with no exercises', function () {
            $user = User::factory()->create();

            $service = app(ExerciseService::class);
            $exercises = $service->getExercisesByUser($user->id);

            $this->assertCount(0, $exercises);
        });

        test('caches results for user exercises (30 minutes)', function () {
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
        });

        test('only returns exercises for specified user', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            Exercise::factory()->count(2)->for($user1)->create();
            Exercise::factory()->count(3)->for($user2)->create();

            $service = app(ExerciseService::class);
            $exercises = $service->getExercisesByUser($user1->id);

            $this->assertCount(2, $exercises);
            $exercises->each(fn($e) => $this->assertEquals($user1->id, $e->user_id));
        });
    });

    describe('getExerciseById', function () {
        test('returns exercise by id', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            $service = app(ExerciseService::class);
            $found = $service->getExerciseById($exercise->id);

            $this->assertTrue($found->is($exercise));
        });

        test('returns null for non-existent exercise', function () {
            $service = app(ExerciseService::class);
            $found = $service->getExerciseById(99999);

            $this->assertNull($found);
        });

        test('caches individual exercise (1 hour)', function () {
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
        });
    });

    describe('createExercise', function () {
        test('creates exercise with valid data', function () {
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
        });

        test('invalidates user cache when creating', function () {
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
        });
    });

    describe('updateExercise', function () {
        test('updates exercise with new data', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create(['name' => 'Old Name']);

            $service = app(ExerciseService::class);
            $updated = $service->updateExercise($exercise->id, ['name' => 'New Name']);

            $this->assertEquals('New Name', $updated->name);
            $this->assertDatabaseHas('exercises', ['id' => $exercise->id, 'name' => 'New Name']);
        });

        test('invalidates caches on update', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            Cache::put("exercise_{$exercise->id}", $exercise, 3600);
            Cache::put("user_{$user->id}_exercises", [$exercise], 3600);

            $service = app(ExerciseService::class);
            $service->updateExercise($exercise->id, ['name' => 'Updated']);

            $this->assertFalse(Cache::has("exercise_{$exercise->id}"));
            $this->assertFalse(Cache::has("user_{$user->id}_exercises"));
        });
    });

    describe('deleteExercise', function () {
        test('deletes exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();
            $exerciseId = $exercise->id;

            $service = app(ExerciseService::class);
            $service->deleteExercise($exerciseId);

            $this->assertDatabaseMissing('exercises', ['id' => $exerciseId]);
        });

        test('invalidates caches on delete', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            Cache::put("exercise_{$exercise->id}", $exercise, 3600);
            Cache::put("user_{$user->id}_exercises", [$exercise], 3600);

            $service = app(ExerciseService::class);
            $service->deleteExercise($exercise->id);

            $this->assertFalse(Cache::has("exercise_{$exercise->id}"));
            $this->assertFalse(Cache::has("user_{$user->id}_exercises"));
        });
    });

    describe('getMuscleGroupsByUser', function () {
        test('returns unique muscle groups for user', function () {
            $user = User::factory()->create();
            Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);
            Exercise::factory()->for($user)->create(['muscleGroup' => 'Back']);
            Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);

            $service = app(ExerciseService::class);
            $groups = $service->getMuscleGroupsByUser($user->id);

            $unique = $groups->unique()->count();
            $this->assertEquals(2, $unique);
        });

        test('returns empty collection for user with no exercises', function () {
            $user = User::factory()->create();

            $service = app(ExerciseService::class);
            $groups = $service->getMuscleGroupsByUser($user->id);

            $this->assertCount(0, $groups);
        });
    });
});

describe('MealService', function () {
    describe('getMealByUser', function () {
        test('returns meals for user', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            Meal::factory()->count(3)->for($user)->for($dailyLog)->create();

            $service = app(MealService::class);
            $meals = $service->getMealByUser($user->id);

            $this->assertCount(3, $meals);
        });

        test('caches user meals (30 minutes)', function () {
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
        });
    });

    describe('getMealById', function () {
        test('returns meal by id', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();

            $service = app(MealService::class);
            $found = $service->getMealById($meal->id);

            $this->assertTrue($found->is($meal));
        });

        test('caches individual meal (1 hour)', function () {
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
        });
    });

    describe('createMeal', function () {
        test('creates meal with auto daily log association', function () {
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
        });

        test('invalidates caches on meal creation', function () {
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
        });
    });

    describe('updateMeal', function () {
        test('updates meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create(['calories' => 100]);

            $service = app(MealService::class);
            $updated = $service->updateMeal($meal->id, ['calories' => 150]);

            $this->assertEquals(150, $updated->calories);
        });
    });

    describe('deleteMeal', function () {
        test('deletes meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();
            $mealId = $meal->id;

            $service = app(MealService::class);
            $service->deleteMeal($mealId);

            $this->assertDatabaseMissing('meals', ['id' => $mealId]);
        });
    });
});
