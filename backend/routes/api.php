<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkoutSplitController;
use App\Http\Controllers\WorkoutSplitExerciseController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\LoggedSetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
});

Route::middleware(['auth:api', 'throttle:200,1'])->group(function () {
    // Auth
    Route::prefix('auth')->middleware('throttle:50,1')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
    });

    // Subscription (user self-service)
    Route::prefix('user')->middleware('api.write')->group(function () {
        Route::patch('/subscription', [UserController::class, 'updateSubscription']);
    });

    // Admin: user data
    Route::prefix('users')->middleware(['role.check', 'api.write'])->group(function () {
        Route::get('/data/count', [UserController::class, 'indexUserData']);
        Route::get('/data/recent', [UserController::class, 'indexRecentUsers']);
    });

    // Meals
    Route::middleware(['api.read'])->get('/meals/user/{userId}', [MealController::class, 'index'])->name('meals.index');
    Route::middleware(['api.read'])->get('/meals/{meal}', [MealController::class, 'show'])->name('meals.show');
    Route::middleware(['api.write'])->post('/meals/user/{userId}', [MealController::class, 'store'])->name('meals.store');
    Route::middleware(['api.write'])->put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::middleware(['api.write'])->delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

    // Daily goals (read-only, computed resource)
    Route::prefix('daily-goals')->middleware('api.read')->group(function () {
        Route::get('/user/{userId}/today', [DailyLogController::class, 'today']);
        Route::get('/user/{userId}/weekly', [DailyLogController::class, 'weekly']);
        Route::get('/user/{userId}/date/{date}', [DailyLogController::class, 'byDate']);
    });

    // User settings
    Route::prefix('settings')->group(function () {
        Route::middleware('api.read')->get('/user/{userId}', [UserSettingsController::class, 'index']);
        Route::middleware('api.write')->put('/user/{userId}', [UserSettingsController::class, 'update']);
    });

    // Exercises (with extra muscle-groups endpoint)
    Route::middleware('api.read')
        ->get('/exercises/muscle-groups', [ExerciseController::class, 'muscleGroups'])
        ->name('exercises.muscleGroups');
    Route::apiResource('exercises', ExerciseController::class);

    // Workout splits
    Route::apiResource('workout-splits', WorkoutSplitController::class);

    // Workout split exercises (nested with parent ownership check)
    Route::prefix('workout-splits/{workoutSplit}/exercises')
        ->middleware('can:manage,workoutSplit')
        ->group(function () {
            Route::middleware('api.read')->group(function () {
                Route::get('/', [WorkoutSplitExerciseController::class, 'index'])
                    ->name('workout-split-exercises.index');
                Route::get('/{exercise}', [WorkoutSplitExerciseController::class, 'show'])
                    ->name('workout-split-exercises.show');
            });
            Route::middleware('api.write')->group(function () {
                Route::post('/', [WorkoutSplitExerciseController::class, 'store'])
                    ->name('workout-split-exercises.store');
                Route::put('/{exercise}', [WorkoutSplitExerciseController::class, 'update'])
                    ->name('workout-split-exercises.update');
                Route::delete('/{exercise}', [WorkoutSplitExerciseController::class, 'destroy'])
                    ->name('workout-split-exercises.destroy');
            });
        });

    // Workouts
    Route::apiResource('workouts', WorkoutController::class);

    // Logged sets (shallow nesting: collection routes nested, member routes flat)
    // POST   /workouts/{workout}/sets   → store
    // GET    /workouts/{workout}/sets   → index
    // GET    /sets/{set}                → show
    // PUT    /sets/{set}                → update
    // DELETE /sets/{set}                → destroy
    Route::prefix('workouts/{workout}/sets')
        ->middleware('can:manage,workout')
        ->group(function () {
            Route::middleware('api.read')->group(function () {
                Route::get('/', [LoggedSetController::class, 'index'])
                    ->name('workouts.sets.index');
            });
            Route::middleware('api.write')->group(function () {
                Route::post('/', [LoggedSetController::class, 'store'])
                    ->name('workouts.sets.store');
            });
        });

    // Shallow routes for individual set management
    Route::prefix('sets')->group(function () {
        Route::middleware('api.read')->group(function () {
            Route::get('/{set}', [LoggedSetController::class, 'show'])
                ->name('sets.show');
        });
        Route::middleware('api.write')->group(function () {
            Route::put('/{set}', [LoggedSetController::class, 'update'])
                ->name('sets.update');
            Route::delete('/{set}', [LoggedSetController::class, 'destroy'])
                ->name('sets.destroy');
        });
    });
});
