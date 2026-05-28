<?php

namespace App\Http\Requests;

use App\Data\CreateLoggedSetData;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoggedSetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'workout_split_exercise_id' => 'required|integer|exists:workout_split_exercises,id',
            'set_number' => 'required|integer|min:1|max:100',
            'reps' => 'nullable|integer|min:0|max:1000',
            'weight' => 'nullable|numeric|min:0|max:999999.99',
            'rpe' => 'nullable|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'workout_split_exercise_id.required' => 'Workout split exercise ID is required',
            'workout_split_exercise_id.integer' => 'Workout split exercise ID must be an integer',
            'workout_split_exercise_id.exists' => 'The selected workout split exercise does not exist',
            'set_number.required' => 'Set number is required',
            'set_number.integer' => 'Set number must be an integer',
            'set_number.min' => 'Set number must be at least 1',
            'set_number.max' => 'Set number cannot exceed 100',
            'reps.integer' => 'Reps must be an integer',
            'reps.min' => 'Reps cannot be negative',
            'reps.max' => 'Reps cannot exceed 1000',
            'weight.numeric' => 'Weight must be a number',
            'weight.min' => 'Weight cannot be negative',
            'weight.max' => 'Weight cannot exceed 999999.99',
            'rpe.integer' => 'RPE must be an integer',
            'rpe.min' => 'RPE must be between 1 and 10',
            'rpe.max' => 'RPE must be between 1 and 10',
        ];
    }

    public function toDto(): CreateLoggedSetData
    {
        return new CreateLoggedSetData(
            workout_split_exercise_id: (int) $this->validated('workout_split_exercise_id'),
            set_number: (int) $this->validated('set_number'),
            reps: $this->validated('reps'),
            weight: $this->validated('weight'),
            rpe: $this->validated('rpe'),
        );
    }
}
