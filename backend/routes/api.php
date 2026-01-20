<?php

use App\Http\Controllers\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\UserSettingsController;



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);


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
        Route::put('/user/{userId}', [UserSettingsController::class, 'update']);
    });
    
    // Exercise Routes
   Route::prefix('exercises')->group(function () {
        Route::get('/', [ExerciseController::class, 'index']);
        Route::post('/', [ExerciseController::class, 'store']);
        Route::get('/{id}', [ExerciseController::class, 'show']);
        Route::put('/{id}', [ExerciseController::class, 'update']);
        Route::delete('/{id}', [ExerciseController::class, 'destroy']);
        Route::get('/muscle-groups', [ExerciseController::class, 'muscleGroups']);
    });

});
