<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoggedSet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'workout_id',
        'workout_split_exercise_id',
        'set_number',
        'reps',
        'weight',
        'rpe',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'set_number' => 'integer',
            'reps' => 'integer',
            'weight' => 'decimal:2',
            'rpe' => 'integer',
        ];
    }

    /**
     * Get the workout this logged set belongs to.
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    /**
     * Get the workout split exercise this logged set is for.
     */
    public function workoutSplitExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutSplitExercise::class);
    }
}
