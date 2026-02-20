<?php

use App\Http\Controllers\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DailyLogController;



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);


Route::middleware(['auth:api'])->group(function () {
    
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/data/count', [UserController::class, 'indexUserData']);
        Route::get('/data/recent', [UserController::class, 'indexRecentUsers']);
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

    Route::prefix('daily-goals')->group(function () {
        Route::get('/user/{userId}/today', [DailyLogController::class, 'today']);
        Route::get('/user/{userId}/weekly', [DailyLogController::class, 'weekly']);
        Route::get('/user/{userId}/date/{date}', [DailyLogController::class, 'byDate']);
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
