<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

// Protected routes
Route::middleware(['auth:api'])->group(function () {
    
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
    });

    // User Routes
    Route::prefix('users')->group(function () {
        Route::get('/data/count', [UserController::class, 'indexUserData']);
        Route::get('/data/recent', [UserController::class, 'indexRecentUsers']);
    });

    // Role Check Route
    Route::middleware('role.check')->get('/role-check', function (Request $request) {
        return response()->json(['success' => 'Accessed admin/editor panel.']);
    });
    
    // Meal Routes with Route Model Binding
    Route::prefix('meals')->group(function () {
        Route::get('/user/{userId}', [MealController::class, 'index'])->name('meals.index');
        Route::post('/user/{userId}', [MealController::class, 'store'])->name('meals.store');
        
        Route::scopeBindings()->group(function () {
            Route::get('/{meal}', [MealController::class, 'show'])->name('meals.show');
            Route::put('/{meal}', [MealController::class, 'update'])->name('meals.update');
            Route::delete('/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
        });
    });

    // Daily Goals Routes
    Route::prefix('daily-goals')->group(function () {
        Route::get('/user/{userId}/today', [DailyLogController::class, 'today']);
        Route::get('/user/{userId}/weekly', [DailyLogController::class, 'weekly']);
        Route::get('/user/{userId}/date/{date}', [DailyLogController::class, 'byDate']);
    });

    // User Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('/user/{userId}', [UserSettingsController::class, 'index']);
        Route::put('/user/{userId}', [UserSettingsController::class, 'update']);
    });
    
    // Exercise Routes with Route Model Binding
    Route::prefix('exercises')->group(function () {
        Route::get('/', [ExerciseController::class, 'index'])->name('exercises.index');
        Route::post('/', [ExerciseController::class, 'store'])->name('exercises.store');
        Route::get('/muscle-groups', [ExerciseController::class, 'muscleGroups'])->name('exercises.muscleGroups');
        
        Route::scopeBindings()->group(function () {
            Route::get('/{exercise}', [ExerciseController::class, 'show'])->name('exercises.show');
            Route::put('/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
            Route::delete('/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
        });
    });
});
