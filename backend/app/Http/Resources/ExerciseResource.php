<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => 'ex_' . $this->id,
            'type' => 'exercise',
            'attributes' => [
                'name' => $this->name,
                'muscle_group' => $this->muscleGroup,
                'description' => $this->description,
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
        ];
    }
}
