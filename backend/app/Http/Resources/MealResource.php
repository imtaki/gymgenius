<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => 'meal_' . $this->id,
            'type' => 'meal',
            'attributes' => [
                'name' => $this->name,
                'category' => $this->category,
                'calories_kcal' => $this->calories,
                'protein_g' => $this->protein,
                'carbs_g' => $this->carbs,
                'fats_g' => $this->fats,
                'date' => $this->date,
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
        ];
    }
}
