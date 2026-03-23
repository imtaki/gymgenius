<?php

use App\Models\DailyLog;
use App\Models\Meal;
use App\Models\User;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('MealController', function () {
    describe('index', function () {
        test('user can list meals for a specific day', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            Meal::factory()->count(3)->for($user)->for($dailyLog)->create();

            $response = $this->actingAs($user)->getJson("/api/meals/user/{$user->id}");

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
            $response->assertJsonCount(3, 'data');
        });

        test('user can only see their own meals', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            $dailyLog1 = DailyLog::factory()->for($user1)->create();
            $dailyLog2 = DailyLog::factory()->for($user2)->create();
            
            Meal::factory()->count(2)->for($user1)->for($dailyLog1)->create();
            Meal::factory()->count(3)->for($user2)->for($dailyLog2)->create();

            $response = $this->actingAs($user1)->getJson("/api/meals/user/{$user1->id}");

            $response->assertJsonCount(2, 'data');
        });

        test('user cannot view another user\'s meals', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->getJson("/api/meals/user/{$user2->id}");

            $response->assertForbidden();
        });
    });

    describe('show', function () {
        test('user can view their own meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();

            $response = $this->actingAs($user)->getJson("/api/meals/{$meal->id}");

            $response->assertOk();
            $response->assertJsonPath('data.id', $meal->id);
            $response->assertJsonPath('data.name', $meal->name);
        });

        test('user cannot view another user\'s meal', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            $dailyLog = DailyLog::factory()->for($user1)->create();
            $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

            $response = $this->actingAs($user2)->getJson("/api/meals/{$meal->id}");

            $response->assertForbidden();
        });
    });

    describe('store', function () {
        test('user can create meal', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
                'name' => 'Chicken Breast',
                'category' => 'Protein',
                'calories' => 165,
                'protein' => 31,
                'carbs' => 0,
                'fats' => 3.6,
            ]);

            $response->assertCreated();
            $this->assertDatabaseHas('meals', [
                'name' => 'Chicken Breast',
                'user_id' => $user->id,
            ]);
        });

        test('create meal validates required fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", []);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['name', 'calories']);
        });

        test('create meal validates numeric fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
                'name' => 'Meal',
                'calories' => 'not-a-number',
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['calories']);
        });

        test('user cannot create meal for another user', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->postJson("/api/meals/user/{$user2->id}", [
                'name' => 'Meal',
                'calories' => 100,
            ]);

            $response->assertForbidden();
        });
    });

    describe('update', function () {
        test('user can update their own meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create([
                'name' => 'Chicken',
                'calories' => 165,
            ]);

            $response = $this->actingAs($user)->putJson("/api/meals/{$meal->id}", [
                'name' => 'Grilled Chicken',
                'calories' => 170,
            ]);

            $response->assertOk();
            $this->assertDatabaseHas('meals', [
                'id' => $meal->id,
                'name' => 'Grilled Chicken',
                'calories' => 170,
            ]);
        });

        test('user cannot update another user\'s meal', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            $dailyLog = DailyLog::factory()->for($user1)->create();
            $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

            $response = $this->actingAs($user2)->putJson("/api/meals/{$meal->id}", [
                'name' => 'Updated',
                'calories' => 100,
            ]);

            $response->assertForbidden();
        });
    });

    describe('destroy', function () {
        test('user can delete their own meal', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create();
            $meal = Meal::factory()->for($user)->for($dailyLog)->create();

            $response = $this->actingAs($user)->deleteJson("/api/meals/{$meal->id}");

            $response->assertOk();
            $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
        });

        test('user cannot delete another user\'s meal', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            $dailyLog = DailyLog::factory()->for($user1)->create();
            $meal = Meal::factory()->for($user1)->for($dailyLog)->create();

            $response = $this->actingAs($user2)->deleteJson("/api/meals/{$meal->id}");

            $response->assertForbidden();
            $this->assertDatabaseHas('meals', ['id' => $meal->id]);
        });
    });

    describe('rate limiting on write operations', function () {
        test('meal creation is rate limited to 30 requests per minute', function () {
            $user = User::factory()->create();

            for ($i = 0; $i < 30; $i++) {
                $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
                    'name' => "Meal $i",
                    'calories' => 100,
                ]);
                $this->assertNotEquals(429, $response->status());
            }

            $response = $this->actingAs($user)->postJson("/api/meals/user/{$user->id}", [
                'name' => 'Meal 31',
                'calories' => 100,
            ]);
            $this->assertEquals(429, $response->status());
        });
    });
});
