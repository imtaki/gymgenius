<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkoutLogRequest extends FormRequest
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
            'exercise_id' => 'integer|exists:exercises,id',
            'date' => 'sometimes|required|date',
            'duration_minutes' => 'required|integer|min:1',
            'calories_burned' => 'required|integer|min:1',

        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required' => 'The exercise field is required.',
            'exercise_id.exists' => 'The selected exercise does not exist.',

            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date.',

            'duration_minutes.required' => 'The duration field is required.',
            'duration_minutes.integer' => 'The duration must be an integer.',
            'duration_minutes.min' => 'The duration must be at least 1 minute.',
            
            'calories_burned.required' => 'The calories burned field is required.',
            'calories_burned.integer' => 'The calories burned must be an integer.',
            'calories_burned.min' => 'The calories burned must be at least 1 calorie.',
        ];
    }
}
