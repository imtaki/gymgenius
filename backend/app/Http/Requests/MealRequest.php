<?php

namespace App\Http\Requests;

use App\Data\CreateMealData;
use App\Data\UpdateMealData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NotInFuture;

class MealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:breakfast,lunch,dinner,snacks',
            'calories' => 'required|integer|min:0',
            'protein' => 'required|integer|min:0',
            'carbs' => 'required|integer|min:0',
            'fats' => 'required|integer|min:0',
            'date' => ['nullable', 'date', new NotInFuture()]
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

    public function toDto(): CreateMealData|UpdateMealData
    {
        if ($this->isMethod('post')) {
            return new CreateMealData(
                name: (string) $this->validated('name'),
                category: (string) $this->validated('category'),
                calories: (int) $this->validated('calories'),
                protein: (int) $this->validated('protein'),
                carbs: (int) $this->validated('carbs'),
                fats: (int) $this->validated('fats'),
                date: $this->validated('date'),
            );
        }

        return UpdateMealData::fromArray($this->validated());
    }
}