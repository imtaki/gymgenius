<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSettings>
 */
class UserSettingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'height' => $this->faker->randomFloat(2, 150, 200),
            'age' => $this->faker->numberBetween(18, 65),
            'goal_type' => $this->faker->randomElement(['cutting', 'maintaining', 'bulking']),
            'caloric_goal' => $this->faker->numberBetween(1500, 3000),
            'current_weight' => $this->faker->randomFloat(2, 50, 120),
            'target_weight' => $this->faker->randomFloat(2, 50, 120),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
