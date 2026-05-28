<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutSplitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'workout_split',
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'exercises_count' => $this->exercises_count ?? $this->exercises()->count(),
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'exercises' => WorkoutSplitExerciseResource::collection($this->whenLoaded('exercises')),
            ],
        ];
    }
}
