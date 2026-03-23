<?php

use App\Models\Meal;
use App\Models\User;
use App\Models\DailyLog;
use Illuminate\Support\Facades\Cache;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('MealObserver', function () {
    describe('cache invalidation on save', function () {
        test('meal cache is invalidated when meal is saved', function () {
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
        });

        test('user meals cache is invalidated when meal is saved', function () {
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
        });
    });

    describe('cache invalidation on delete', function () {
        test('meal cache is cleared when meal is deleted', function () {
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
        });
    });
});

describe('UserSettingsObserver', function () {
    describe('cache invalidation on save', function () {
        test('settings cache is invalidated when settings are saved', function () {
            $user = User::factory()->create();
            $settings = $user->settings;

            // Set cache
            Cache::put("setting_{$settings->id}", $settings, 3600);
            $this->assertTrue(Cache::has("setting_{$settings->id}"));

            // Update settings
            $settings->update(['age' => 30]);

            // Cache should be cleared
            $this->assertFalse(Cache::has("setting_{$settings->id}"));
        });

        test('user settings cache is invalidated when settings are saved', function () {
            $user = User::factory()->create();
            $settings = $user->settings;

            // Set cache
            Cache::put("user_{$user->id}_settings", $settings, 3600);
            $this->assertTrue(Cache::has("user_{$user->id}_settings"));

            // Update settings
            $settings->update(['height' => 180]);

            // Cache should be cleared
            $this->assertFalse(Cache::has("user_{$user->id}_settings"));
        });
    });

    describe('cache invalidation on delete', function () {
        test('settings cache is cleared when settings are deleted', function () {
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
        });
    });
});
