<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutSplit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    /**
     * Get the user that owns this workout split.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exercises for this workout split.
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutSplitExercise::class);
    }

    /**
     * Get the workouts using this split.
     */
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }
}
