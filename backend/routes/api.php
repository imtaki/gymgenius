<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\UserController;
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
});
