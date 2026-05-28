<?php

namespace App\Http\Requests;

use App\Data\CreateWorkoutData;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'workout_split_id' => 'nullable|integer|exists:workout_splits,id',
            'date' => 'required|date',
            'started_at' => 'nullable|date_format:Y-m-d H:i:s',
            'ended_at' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:started_at',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'workout_split_id.integer' => 'Workout split ID must be an integer',
            'workout_split_id.exists' => 'The selected workout split does not exist',
            'date.required' => 'Workout date is required',
            'date.date' => 'Workout date must be a valid date',
            'started_at.date_format' => 'Started at must be in format: YYYY-MM-DD HH:MM:SS',
            'ended_at.date_format' => 'Ended at must be in format: YYYY-MM-DD HH:MM:SS',
            'ended_at.after_or_equal' => 'Ended at must be after or equal to started at',
            'notes.string' => 'Notes must be a string',
            'notes.max' => 'Notes cannot exceed 1000 characters',
        ];
    }

    public function toDto(): CreateWorkoutData
    {
        return new CreateWorkoutData(
            workout_split_id: $this->validated('workout_split_id'),
            date: (string) $this->validated('date'),
            started_at: $this->validated('started_at'),
            ended_at: $this->validated('ended_at'),
            notes: $this->validated('notes'),
        );
    }
}
