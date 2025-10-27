<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('/user', [AuthController::class, 'getUser'])->middleware('auth:api');
Route::post('/register', [AuthController::class, 'register']);

// Email
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

//Exercise 
Route::get('/exercise', [ExerciseController::class, 'getExercises']);
Route::post('/exercise', [ExerciseController::class, 'createExercises']);