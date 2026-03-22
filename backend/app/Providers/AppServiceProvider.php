<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Meal;
use App\Models\Exercise;
use App\Models\UserSettings;
use App\Models\DailyLog;
use App\Models\WorkoutLog;
use App\Models\WorkoutProgram;
use App\Models\WorkoutStreak;
use App\Observers\MealObserver;
use App\Observers\UserSettingsObserver;
use App\Policies\MealPolicy;
use App\Policies\ExercisePolicy;
use App\Policies\UserSettingsPolicy;
use App\Policies\DailyLogPolicy;
use App\Policies\WorkoutLogPolicy;
use App\Policies\WorkoutProgramPolicy;
use App\Policies\WorkoutStreakPolicy;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Meal::class => MealPolicy::class,
        Exercise::class => ExercisePolicy::class,
        UserSettings::class => UserSettingsPolicy::class,
        DailyLog::class => DailyLogPolicy::class,
        WorkoutLog::class => WorkoutLogPolicy::class,
        WorkoutProgram::class => WorkoutProgramPolicy::class,
        WorkoutStreak::class => WorkoutStreakPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Register model observers
        Meal::observe(MealObserver::class);
        UserSettings::observe(UserSettingsObserver::class);
    }
}
