<?php

use App\Models\DailyLog;
use App\Models\User;
use App\Models\Meal;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('DailyLogController', function () {
    describe('today', function () {
        test('user can get today\'s daily log', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
        });

        test('unauthenticated user cannot access today endpoint', function () {
            $response = $this->getJson('/api/daily-goals/user/1/today');

            $response->assertUnauthorized();
        });

        test('user cannot access another user\'s today log', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->getJson("/api/daily-goals/user/{$user2->id}/today");

            $response->assertForbidden();
        });

        test('today endpoint includes meals', function () {
            $user = User::factory()->create();
            $dailyLog = DailyLog::factory()->for($user)->create(['date' => now()->format('Y-m-d')]);
            Meal::factory()->count(2)->for($user)->for($dailyLog)->create();

            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data' => ['meals']]);
        });
    });

    describe('weekly', function () {
        test('user can get weekly log', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/weekly");

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
        });

        test('user cannot access another user\'s weekly log', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->getJson("/api/daily-goals/user/{$user2->id}/weekly");

            $response->assertForbidden();
        });
    });

    describe('byDate', function () {
        test('user can get log by specific date', function () {
            $user = User::factory()->create();
            $date = '2026-03-20';

            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/date/{$date}");

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
        });

        test('user cannot access another user\'s date-specific log', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->getJson("/api/daily-goals/user/{$user2->id}/date/2026-03-20");

            $response->assertForbidden();
        });

        test('invalid date format is rejected', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/date/invalid-date");

            $response->assertNotFound();
        });
    });

    describe('rate limiting on read operations', function () {
        test('daily goals read operations have 300 requests per minute limit', function () {
            $user = User::factory()->create();

            for ($i = 0; $i < 300; $i++) {
                $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");
                $this->assertNotEquals(429, $response->status());
            }

            $response = $this->actingAs($user)->getJson("/api/daily-goals/user/{$user->id}/today");
            $this->assertEquals(429, $response->status());
        });
    });
});

describe('UserSettingsController', function () {
    describe('index', function () {
        test('user can get their settings', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->getJson("/api/settings/user/{$user->id}");

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
            $response->assertJsonPath('data.user_id', $user->id);
        });

        test('user cannot view another user\'s settings', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->getJson("/api/settings/user/{$user2->id}");

            $response->assertForbidden();
        });

        test('unauthenticated user cannot access settings', function () {
            $response = $this->getJson('/api/settings/user/1');

            $response->assertUnauthorized();
        });
    });

    describe('update', function () {
        test('user can update their settings', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
                'height' => 185,
                'age' => 28,
                'current_weight' => 78.5,
            ]);

            $response->assertOk();
            $this->assertDatabaseHas('user_settings', [
                'user_id' => $user->id,
                'height' => 185,
                'age' => 28,
            ]);
        });

        test('user cannot update another user\'s settings', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $response = $this->actingAs($user1)->putJson("/api/settings/user/{$user2->id}", [
                'height' => 185,
            ]);

            $response->assertForbidden();
        });

        test('update validates numeric fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
                'age' => 'not-a-number',
            ]);

            $response->assertUnprocessable();
        });
    });

    describe('rate limiting', function () {
        test('settings update is rate limited to 30 requests per minute', function () {
            $user = User::factory()->create();

            for ($i = 0; $i < 30; $i++) {
                $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
                    'age' => 25 + $i,
                ]);
                $this->assertNotEquals(429, $response->status());
            }

            $response = $this->actingAs($user)->putJson("/api/settings/user/{$user->id}", [
                'age' => 60,
            ]);
            $this->assertEquals(429, $response->status());
        });
    });
});

describe('WorkoutProgramController', function () {
    describe('index', function () {
        test('user can list their workout programs', function () {
            $user = User::factory()->create();
            \App\Models\WorkoutProgram::factory()->count(3)->for($user)->create();

            $response = $this->actingAs($user)->getJson('/api/workout-programs');

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
        });

        test('unauthenticated user cannot list programs', function () {
            $response = $this->getJson('/api/workout-programs');

            $response->assertUnauthorized();
        });
    });

    describe('show', function () {
        test('user can view their workout program', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $response = $this->actingAs($user)->getJson("/api/workout-programs/{$program->id}");

            $response->assertOk();
            $response->assertJsonPath('data.id', $program->id);
        });

        test('user cannot view another user\'s program', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user1)->create();

            $response = $this->actingAs($user2)->getJson("/api/workout-programs/{$program->id}");

            $response->assertForbidden();
        });
    });

    describe('store', function () {
        test('user can create workout program', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson('/api/workout-programs', [
                'name' => 'Push/Pull/Legs',
            ]);

            $response->assertCreated();
            $this->assertDatabaseHas('workout_programs', [
                'name' => 'Push/Pull/Legs',
                'user_id' => $user->id,
            ]);
        });

        test('create program validates required fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson('/api/workout-programs', []);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['name']);
        });
    });

    describe('update', function () {
        test('user can update their program', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $response = $this->actingAs($user)->putJson("/api/workout-programs/{$program->id}", [
                'name' => 'Updated Name',
            ]);

            $response->assertOk();
            $this->assertDatabaseHas('workout_programs', [
                'id' => $program->id,
                'name' => 'Updated Name',
            ]);
        });

        test('user cannot update another user\'s program', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user1)->create();

            $response = $this->actingAs($user2)->putJson("/api/workout-programs/{$program->id}", [
                'name' => 'Updated',
            ]);

            $response->assertForbidden();
        });
    });

    describe('destroy', function () {
        test('user can delete their program', function () {
            $user = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user)->create();

            $response = $this->actingAs($user)->deleteJson("/api/workout-programs/{$program->id}");

            $response->assertOk();
            $this->assertDatabaseMissing('workout_programs', ['id' => $program->id]);
        });

        test('user cannot delete another user\'s program', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $program = \App\Models\WorkoutProgram::factory()->for($user1)->create();

            $response = $this->actingAs($user2)->deleteJson("/api/workout-programs/{$program->id}");

            $response->assertForbidden();
        });
    });
});
