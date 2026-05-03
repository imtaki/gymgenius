<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => 'log_' . $this->id,
            'type' => 'daily_log',
            'attributes' => [
                'date' => $this->date,
                'calories_kcal' => $this->calories,
                'protein_g' => $this->protein,
                'carbs_g' => $this->carbs,
                'fats_g' => $this->fats,
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'meals' => MealResource::collection($this->whenLoaded('meals')),
            ],
        ];
    }
}
