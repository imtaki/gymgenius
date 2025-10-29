<?php

namespace Database\Seeders;

use App\Models\Meal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Meal::factory()->create([
            'name' => 'Protein Oatmel',
            'calories' => 700,
            'protein' => '40',
            'carbs'=> '90',
            'fats' => '35'
        ]);
    }
}
