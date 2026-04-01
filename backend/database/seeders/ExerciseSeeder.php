<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();

        if ($user) {
            Exercise::factory()->create([
                'user_id' => $user->id,
                'name' => 'Pec Fly',
                'muscleGroup' => 'Chest',
                'description' => 'Best exercise for overall chest growth',
            ]);
        }
    }
}
