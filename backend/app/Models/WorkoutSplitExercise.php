<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutSplitExercise extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'workout_split_id',
        'exercise_id',
        'order',
        'target_sets',
        'target_reps',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'target_sets' => 'integer',
            'target_reps' => 'integer',
        ];
    }

    /**
     * Get the workout split this exercise belongs to.
     */
    public function workoutSplit(): BelongsTo
    {
        return $this->belongsTo(WorkoutSplit::class);
    }

    /**
     * Get the exercise.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Get the logged sets for this exercise in workouts.
     */
    public function loggedSets(): HasMany
    {
        return $this->hasMany(LoggedSet::class);
    }
}
