<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Models\Meal;  

class DailyLog extends Model
{
    protected $fillable = ['user_id', 'date', 'calorie_goal'];

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
}
