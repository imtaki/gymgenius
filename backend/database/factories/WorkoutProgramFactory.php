<?php

namespace Database\Factories;

use App\Models\WorkoutProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutProgram>
 */
class WorkoutProgramFactory extends Factory
{
    protected $model = WorkoutProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Program',
        ];
    }
}
