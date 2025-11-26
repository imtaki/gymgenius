<?php

use App\Http\Controllers\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\UserSettingsController;



// Public Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

Route::apiResource('meals', MealController::class)->middleware('auth:api');

// Protected Routes (Require Authentication)
Route::middleware(['auth:api'])->group(function () {
    
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
    });
    
    // Role Check Route
    Route::middleware('role.check')->get('/role-check', function (Request $request) {
        return response()->json(['success' => 'Accessed admin/editor panel.']);
    });
    
    // Meal Routes
    Route::prefix('meals')->group(function () {
        Route::get('/user/{id}', [MealController::class, 'index']);
        Route::get('/{id}', [MealController::class, 'show']);
        Route::post('/user/{userId}', [MealController::class, 'store']);
        Route::put('/{id}', [MealController::class, 'update']);
        Route::delete('/{id}', [MealController::class, 'destroy']);
    });

    Route::prefix('settings')->group(function () {
        Route::get('/user/{userId}', [UserSettingsController::class, 'index']);
        Route::post('/user/{userId}', [UserSettingsController::class, 'store']);
        Route::put('/user/{userId}', [UserSettingsController::class, 'update']);
    });
    
    // Exercise Routes
    Route::prefix('exercises')->group(function () {
        Route::get('/', [ExerciseController::class, 'getExercises']);
        Route::post('/', [ExerciseController::class, 'createExercises']);
        Route::get('/muscle-groups', [ExerciseController::class, 'getMuscleGroups']);
    });

});
