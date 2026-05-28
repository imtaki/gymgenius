<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'workout',
            'attributes' => [
                'date' => $this->date?->toDateString(),
                'started_at' => $this->started_at?->toIso8601String(),
                'ended_at' => $this->ended_at?->toIso8601String(),
                'notes' => $this->notes,
                'logged_sets_count' => $this->logged_sets_count ?? $this->loggedSets()->count(),
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'workout_split' => new WorkoutSplitResource($this->whenLoaded('workoutSplit')),
                'logged_sets' => LoggedSetResource::collection($this->whenLoaded('loggedSets')),
            ],
        ];
    }
}
