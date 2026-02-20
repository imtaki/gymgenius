<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    protected $fillable = ['user_id', 'date', 'calorie_goal'];

    protected $casts = [
        'date' => 'date',
    ];

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
}
