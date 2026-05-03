<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Enums\GoalType;
use App\Models\User;

class UserSettings extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'settings_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'height',
        'age',
        'caloric_goal',
        'goal_type',
        'current_weight',
        'target_weight',
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'height' => 'float',
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
