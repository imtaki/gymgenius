<?php

use App\Http\Controllers\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;



// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('/user', [AuthController::class, 'getUser'])->middleware('auth:api');
Route::post('/register', [AuthController::class, 'register'])->middleware('auth:api');
// Get User Meals
Route::get('/users/{id}/meals', [MealController::class, 'getMealByUser']);

// Email
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

//Exercise
Route::get('/exercises', [ExerciseController::class, 'getExercises']);
Route::post('/exercise', [ExerciseController::class, 'createExercises']);
Route::get('/exercises/muscle-groups', [ExerciseController::class, 'getMuscleGroups']);
// Meal
Route::get('/meals/{id}', [MealController::class, 'getMealById']);
Route::post('/users/{userId}/meals', [MealController::class, 'createMeal']);
Route::delete('/meals{id}', [MealController::class, 'deleteMeal']);

