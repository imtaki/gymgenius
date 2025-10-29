<?php

namespace Database\Factories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Meal::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'user_id' => UserFactory::new(),
            'calories' => $this->faker->randomFloat(2, 2000),
            'protein' => $this->faker->randomFloat(2, 100),
            'carbs' => $this->faker->randomFloat(2, 300),
            'fats' => $this->faker->randomFloat(2, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
