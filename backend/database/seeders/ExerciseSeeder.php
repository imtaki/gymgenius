<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exercise::factory()->create([
            'name' => 'Pec Fly',
            'muscleGroup' => 'Chest',
            'description' => 'Best exercise for overall chest growth',
        ]);
    }
}
