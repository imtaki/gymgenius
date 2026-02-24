<?php

namespace App\Services;

use App\Models\WorkoutProgram;
use App\Models\User;

class WorkoutProgramService
{
    public function getAllWorkoutPrograms(User $user, $perPage = 15)
    {
        return WorkoutProgram::where('user_id', $user->id)
            ->with('workouts')
            ->latest()
            ->paginate($perPage);
    }

    public function getWorkoutProgramById(int $id)
    {
        return WorkoutProgram::with('workouts.exercise')->findOrFail($id);
    }

    public function createWorkoutProgram(User $user, array $data)
    {
        return WorkoutProgram::create([
            'user_id' => $user->id,
            'name' => $data['name'],
        ]);
    }

    public function updateWorkoutProgram(WorkoutProgram $program, array $data)
    {
        $program->update($data);
        return $program->fresh();
    }

    public function deleteWorkoutProgram(WorkoutProgram $program)
    {
        return $program->delete();
    }

    public function getProgramStats(WorkoutProgram $program)
    {
        return [
            'total_workouts' => $program->workouts()->count(),
            'total_duration' => $program->workouts()->sum('duration_minutes'),
            'total_calories' => $program->workouts()->sum('calories_burned'),
        ];
    }
}