<?php

namespace App\Http\Requests;

use App\Data\UpdateWorkoutSplitExerciseData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkoutSplitExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => 'sometimes|integer|exists:exercises,id',
            'order' => 'sometimes|integer|min:0',
            'target_sets' => 'nullable|integer|min:1|max:100',
            'target_reps' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.integer' => 'Exercise ID must be an integer',
            'exercise_id.exists' => 'The selected exercise does not exist',
            'order.integer' => 'Order must be an integer',
            'order.min' => 'Order cannot be negative',
            'target_sets.integer' => 'Target sets must be an integer',
            'target_sets.min' => 'Target sets must be at least 1',
            'target_reps.integer' => 'Target reps must be an integer',
            'target_reps.min' => 'Target reps must be at least 1',
            'notes.string' => 'Notes must be a string',
            'notes.max' => 'Notes cannot exceed 1000 characters',
        ];
    }

    public function toDto(): UpdateWorkoutSplitExerciseData
    {
        return new UpdateWorkoutSplitExerciseData(
            exercise_id: $this->validated('exercise_id') ? (int) $this->validated('exercise_id') : null,
            order: $this->validated('order') ? (int) $this->validated('order') : null,
            target_sets: $this->validated('target_sets'),
            target_reps: $this->validated('target_reps'),
            notes: $this->validated('notes'),
        );
    }
}
