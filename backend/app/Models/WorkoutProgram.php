<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutProgram extends Model
{
    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function workouts() {
        return $this->hasMany(WorkoutLog::class);
    }
}
