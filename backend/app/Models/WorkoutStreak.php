<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutStreak extends Model
{
    /** @use HasFactory<\Database\Factories\WorkoutStreakFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_workout_date',
    ];

    protected $casts = [
        'last_workout_date' => 'date',
    ];
    
    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }
}
