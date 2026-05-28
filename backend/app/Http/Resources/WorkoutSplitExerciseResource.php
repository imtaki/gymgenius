<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutSplitExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'workout_split_exercise',
            'attributes' => [
                'order' => $this->order,
                'target_sets' => $this->target_sets,
                'target_reps' => $this->target_reps,
                'notes' => $this->notes,
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'exercise' => new ExerciseResource($this->whenLoaded('exercise')),
                'workout_split_id' => $this->workout_split_id,
            ],
        ];
    }
}
