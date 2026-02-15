<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Models\User;
use Models\DailyLog;

class Meal extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'daily_log_id',
        'category',
        'calories',
        'protein',
        'carbs',
        'fats'
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein'=> 'integer',
        'carbs' => 'float',
        'fats' => 'float'
    ];

    public function users() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dailyLog() {
        return $this->belongsTo(DailyLog::class, 'daily_log_id');
    }
}
