<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoggedSetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'logged_set',
            'attributes' => [
                'set_number' => $this->set_number,
                'reps' => $this->reps,
                'weight' => $this->weight,
                'rpe' => $this->rpe,
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'workout_split_exercise' => new WorkoutSplitExerciseResource($this->whenLoaded('workoutSplitExercise')),
                'workout_id' => $this->workout_id,
            ],
        ];
    }
}
