<?php

namespace Tests\Unit;

use App\Models\DailyLog;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ObserversTest extends TestCase
{
    use RefreshDatabase;

    // MealObserver cache invalidation on save tests
    public function test_meal_cache_is_invalidated_when_meal_is_saved(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        // Set cache
        Cache::put("meal_{$meal->id}", $meal, 3600);
        $this->assertTrue(Cache::has("meal_{$meal->id}"));

        // Update meal to trigger observer
        $meal->update(['name' => 'Updated Meal']);

        // Cache should be cleared
        $this->assertFalse(Cache::has("meal_{$meal->id}"));
    }

    public function test_user_meals_cache_is_invalidated_when_meal_is_saved(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();

        // Set cache
        Cache::put("user_{$user->id}_meals", [$meal], 3600);
        $this->assertTrue(Cache::has("user_{$user->id}_meals"));

        // Update meal
        $meal->update(['calories' => 200]);

        // Cache should be cleared
        $this->assertFalse(Cache::has("user_{$user->id}_meals"));
    }

    // MealObserver cache invalidation on delete tests
    public function test_meal_cache_is_cleared_when_meal_is_deleted(): void
    {
        $user = User::factory()->create();
        $dailyLog = DailyLog::factory()->for($user)->create();
        $meal = Meal::factory()->for($user)->for($dailyLog)->create();
        $mealId = $meal->id;

        // Set cache
        Cache::put("meal_{$mealId}", $meal, 3600);
        Cache::put("user_{$user->id}_meals", [$meal], 3600);

        // Delete meal
        $meal->delete();

        // Both caches should be cleared
        $this->assertFalse(Cache::has("meal_{$mealId}"));
        $this->assertFalse(Cache::has("user_{$user->id}_meals"));
    }

    // UserSettingsObserver cache invalidation on save tests
    public function test_settings_cache_is_invalidated_when_settings_are_saved(): void
    {
        $user = User::factory()->create();
        $settings = $user->settings;

        // Set cache
        Cache::put("setting_{$settings->id}", $settings, 3600);
        $this->assertTrue(Cache::has("setting_{$settings->id}"));

        // Update settings
        $settings->update(['age' => 30]);

        // Cache should be cleared
        $this->assertFalse(Cache::has("setting_{$settings->id}"));
    }

    public function test_user_settings_cache_is_invalidated_when_settings_are_saved(): void
    {
        $user = User::factory()->create();
        $settings = $user->settings;

        // Set cache
        Cache::put("user_{$user->id}_settings", $settings, 3600);
        $this->assertTrue(Cache::has("user_{$user->id}_settings"));

        // Update settings
        $settings->update(['height' => 180]);

        // Cache should be cleared
        $this->assertFalse(Cache::has("user_{$user->id}_settings"));
    }

    // UserSettingsObserver cache invalidation on delete tests
    public function test_settings_cache_is_cleared_when_settings_are_deleted(): void
    {
        $user = User::factory()->create();
        $settings = $user->settings;
        $settingsId = $settings->id;

        // Set cache
        Cache::put("setting_{$settingsId}", $settings, 3600);
        Cache::put("user_{$user->id}_settings", $settings, 3600);

        // Delete settings (cascade)
        $settings->delete();

        // Both caches should be cleared
        $this->assertFalse(Cache::has("setting_{$settingsId}"));
        $this->assertFalse(Cache::has("user_{$user->id}_settings"));
    }
}
