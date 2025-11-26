<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\GoalType;

class UserSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'height',
        'weight',
        'age',
        'caloric_goal',
        'goal_type',
        'current_weight',
        'target_weight',
    ];

    protected $casts = [
        'height' => 'float',
        'weight' => 'integer',
        'age'=> 'integer',
        'caloric_goal' => 'integer',
        'goal_type' => GoalType::class,
        'current_weight' => 'integer',
        'target_weight' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
