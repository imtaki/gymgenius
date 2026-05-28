<?php

namespace App\Http\Requests;

use App\Data\CreateWorkoutSplitData;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutSplitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Workout split name is required',
            'name.string' => 'Workout split name must be a string',
            'name.max' => 'Workout split name cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description cannot exceed 1000 characters',
        ];
    }

    public function toDto(): CreateWorkoutSplitData
    {
        return new CreateWorkoutSplitData(
            name: (string) $this->validated('name'),
            description: $this->validated('description'),
        );
    }
}
