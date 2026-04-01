<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Enums\UserType;
use App\Models\Meal;
use App\Models\UserSettings;
use App\Models\WorkoutLog;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserType::class,
        ];
    }
    // JWT Implementation
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'username' => $this->name,
            'role' => $this->role,
            'email' => $this->email,
        ];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $user->settings()->create([
                'user_id' => $user->id,
                'height' => 175.00,
                'age' => 18,
                'goal_type' => "maintaining",
                'caloric_goal' => 2500,
                'current_weight' => 70.00,
                'target_weight' => 70.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    // Other table relationships

    public function meals() {
        return $this->hasMany(Meal::class);
    }

    public function settings() {
        return $this->hasOne(UserSettings::class);
    }

    public function workouts() {
        return $this->hasMany(WorkoutLog::class);
    }

    public function workoutPrograms() {
        return $this->hasMany(WorkoutProgram::class);
    }

    public function workoutStreak() {
        return $this->hasOne(WorkoutStreak::class);
    }
}
