<?php

namespace App\Http\Requests;

use App\Data\CreateExerciseData;
use Illuminate\Foundation\Http\FormRequest;

class ExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'muscleGroup' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Exercise name is required',
            'name.string' => 'Exercise name must be a string',
            'name.max' => 'Exercise name cannot exceed 255 characters',
            'muscleGroup.required' => 'Muscle group is required',
            'muscleGroup.string' => 'Muscle group must be a string',
            'muscleGroup.max' => 'Muscle group cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description cannot exceed 1000 characters'
        ];
    }

    public function toDto(): CreateExerciseData
    {
        return new CreateExerciseData(
            name: (string) $this->validated('name'),
            muscleGroup: (string) $this->validated('muscleGroup'),
            description: $this->validated('description'),
        );
    }
}
