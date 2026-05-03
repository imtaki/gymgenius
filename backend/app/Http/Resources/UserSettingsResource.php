<?php

namespace App\Http\Resources;

use App\Enums\GoalType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => 'set_' . $this->settings_id,
            'type' => 'user_settings',
            'attributes' => [
                'height_cm' => $this->height,
                'age' => $this->age,
                'caloric_goal_kcal' => $this->caloric_goal,
                'goal_type' => $this->goal_type->value,
                'current_weight_kg' => $this->current_weight,
                'target_weight_kg' => $this->target_weight,
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'meta' => [
                'goal_type_options' => collect(GoalType::cases())->map(fn($case) => $case->value)->values()->toArray(),
                'units' => 'metric',
            ]
        ];
    }
}
