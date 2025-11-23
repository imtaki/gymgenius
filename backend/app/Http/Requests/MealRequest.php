<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:breakfast,lunch,dinner,snacks',
            'calories' => 'required|integer|min:0',
            'protein' => 'required|integer|min:0',
            'carbs' => 'required|integer|min:0',
            'fats' => 'required|integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Meal name is required',
            'category.in' => 'Category must be one of: breakfast, lunch, dinner, snacks',
            'calories.min' => 'Calories cannot be negative',
            'protein.min' => 'Protein cannot be negative',
            'carbs.min' => 'Carbs cannot be negative',
            'fats.min' => 'Fats cannot be negative',
        ];
    }
}