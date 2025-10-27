<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function createExercises(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'muscleGroup' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            $exercise = Exercise::create([
                'name' => $validated['name'],
                'muscleGroup' => $validated['muscleGroup'],
                'description' => $validated['description'],
            ]);
        } catch (\Exception $error) {
            return response()->json(['error' => 'could_not_create_exercise'], 500);
        }

        return response()->json([
            'message' => 'exercise registered successfully',
            'exercise' => $exercise,
        ], 201);
    }

    public function getExercises(Request $request)
    {
        $exercises = Exercise::all();
        return response()->json($exercises);
    }
}
