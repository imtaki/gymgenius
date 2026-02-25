<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'exercise_id' => 'required|exists:exercises,id',
             'date' => 'required|date',
             'duration_minutes' => 'required|integer|min:1',
             'calories_burned' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required' => 'You cannot remove the exercise; please select a valid one.',
            'exercise_id.exists' => 'The selected exercise is invalid or no longer exists.',
            
            'date.required' => 'You cannot clear the date; please provide a valid workout date.',
            'date.date' => 'The workout date must be a valid date format.',
            
            'duration_minutes.integer' => 'The duration must be a whole number (e.g., 45).',
            'duration_minutes.min' => 'If you update the duration, it must be at least 1 minute.',
            
            'calories_burned.integer' => 'Calories burned must be a whole number.',
            'calories_burned.min' => 'If you update the calories, it must be at least 1.',
        ];
    }
}
