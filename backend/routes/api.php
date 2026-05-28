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
    Route::prefix('auth')->middleware('throttle:50,1')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
    });


    Route::prefix('users')->middleware('throttle:300,1')->group(function () {
        Route::get('/data/count', [UserController::class, 'indexUserData']);
        Route::get('/data/recent', [UserController::class, 'indexRecentUsers']);
    });


    Route::middleware(['role.check', 'throttle:100,1'])->get('/role-check', function (Request $request) {
        return response()->json(['success' => 'Accessed admin/editor panel.']);
    });


    Route::prefix('meals')->group(function () {
        // Read operations - Higher limit (300/minute)
        Route::middleware('throttle:300,1')->group(function () {
            Route::get('/user/{userId}', [MealController::class, 'index'])->name('meals.index');
            Route::scopeBindings()->group(function () {
                Route::get('/{meal}', [MealController::class, 'show'])->name('meals.show');
            });
        });


        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/user/{userId}', [MealController::class, 'store'])->name('meals.store');
            Route::scopeBindings()->group(function () {
                Route::put('/{meal}', [MealController::class, 'update'])->name('meals.update');
                Route::delete('/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
            });
        });
    });


    Route::prefix('daily-goals')->middleware('throttle:300,1')->group(function () {
        Route::get('/user/{userId}/today', [DailyLogController::class, 'today']);
        Route::get('/user/{userId}/weekly', [DailyLogController::class, 'weekly']);
        Route::get('/user/{userId}/date/{date}', [DailyLogController::class, 'byDate']);
    });


    Route::prefix('settings')->group(function () {
        Route::middleware('throttle:100,1')->get('/user/{userId}', [UserSettingsController::class, 'index']);
        Route::middleware('throttle:30,1')->put('/user/{userId}', [UserSettingsController::class, 'update']);
    });


    Route::prefix('exercises')->group(function () {
        Route::middleware('throttle:300,1')->group(function () {
            Route::get('/', [ExerciseController::class, 'index'])->name('exercises.index');
            Route::get('/muscle-groups', [ExerciseController::class, 'muscleGroups'])->name('exercises.muscleGroups');
            Route::scopeBindings()->group(function () {
                Route::get('/{exercise}', [ExerciseController::class, 'show'])->name('exercises.show');
            });
        });


        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/', [ExerciseController::class, 'store'])->name('exercises.store');
            Route::scopeBindings()->group(function () {
                Route::put('/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
                Route::delete('/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
            });
        });
    });

    // Workout Splits Routes
    Route::prefix('workout-splits')->group(function () {
        Route::middleware('throttle:300,1')->group(function () {
            Route::get('/', [WorkoutSplitController::class, 'index'])->name('workout-splits.index');
            Route::scopeBindings()->group(function () {
                Route::get('/{workoutSplit}', [WorkoutSplitController::class, 'show'])->name('workout-splits.show');
            });
        });

        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/', [WorkoutSplitController::class, 'store'])->name('workout-splits.store');
            Route::scopeBindings()->group(function () {
                Route::put('/{workoutSplit}', [WorkoutSplitController::class, 'update'])->name('workout-splits.update');
                Route::delete('/{workoutSplit}', [WorkoutSplitController::class, 'destroy'])->name('workout-splits.destroy');
            });
        });
    });

    // Workout Split Exercises Routes (nested under workout splits)
    Route::prefix('workout-splits/{workoutSplit}/exercises')->group(function () {
        Route::middleware('throttle:300,1')->group(function () {
            Route::get('/', [WorkoutSplitExerciseController::class, 'index'])->name('workout-split-exercises.index');
            Route::scopeBindings()->group(function () {
                Route::get('/{exercise}', [WorkoutSplitExerciseController::class, 'show'])->name('workout-split-exercises.show');
            });
        });

        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/', [WorkoutSplitExerciseController::class, 'store'])->name('workout-split-exercises.store');
            Route::scopeBindings()->group(function () {
                Route::put('/{exercise}', [WorkoutSplitExerciseController::class, 'update'])->name('workout-split-exercises.update');
                Route::delete('/{exercise}', [WorkoutSplitExerciseController::class, 'destroy'])->name('workout-split-exercises.destroy');
            });
        });
    });

    // Workouts Routes
    Route::prefix('workouts')->group(function () {
        Route::middleware('throttle:300,1')->group(function () {
            Route::get('/', [WorkoutController::class, 'index'])->name('workouts.index');
            Route::scopeBindings()->group(function () {
                Route::get('/{workout}', [WorkoutController::class, 'show'])->name('workouts.show');
            });
        });

        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/', [WorkoutController::class, 'store'])->name('workouts.store');
            Route::scopeBindings()->group(function () {
                Route::put('/{workout}', [WorkoutController::class, 'update'])->name('workouts.update');
                Route::delete('/{workout}', [WorkoutController::class, 'destroy'])->name('workouts.destroy');
            });
        });
    });

    // Logged Sets Routes (nested under workouts)
    Route::prefix('workouts/{workout}/sets')->group(function () {
        Route::middleware('throttle:300,1')->group(function () {
            Route::get('/', [LoggedSetController::class, 'index'])->name('logged-sets.index');
        });

        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/', [LoggedSetController::class, 'store'])->name('logged-sets.store');
        });
    });

    // Direct logged sets routes (for updating/deleting specific sets)
    Route::prefix('sets')->group(function () {
        Route::middleware('throttle:300,1')->group(function () {
            Route::scopeBindings()->group(function () {
                Route::get('/{set}', [LoggedSetController::class, 'show'])->name('sets.show');
            });
        });

        Route::middleware('throttle:30,1')->group(function () {
            Route::scopeBindings()->group(function () {
                Route::put('/{set}', [LoggedSetController::class, 'update'])->name('sets.update');
                Route::delete('/{set}', [LoggedSetController::class, 'destroy'])->name('sets.destroy');
            });
        });
    });
});
