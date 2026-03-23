<?php

use App\Models\Exercise;
use App\Models\User;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('ExerciseController', function () {
    describe('index', function () {
        test('authenticated user can list their exercises', function () {
            $user = User::factory()->create();
            Exercise::factory()->count(3)->for($user)->create();

            $response = $this->actingAs($user)->getJson('/api/exercises');

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
            $response->assertJsonCount(3, 'data');
        });

        test('user only sees their own exercises', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            
            Exercise::factory()->count(2)->for($user1)->create();
            Exercise::factory()->count(3)->for($user2)->create();

            $response = $this->actingAs($user1)->getJson('/api/exercises');

            $response->assertJsonCount(2, 'data');
        });

        test('unauthenticated user cannot list exercises', function () {
            $response = $this->getJson('/api/exercises');

            $response->assertUnauthorized();
        });
    });

    describe('show', function () {
        test('user can view their own exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            $response = $this->actingAs($user)->getJson("/api/exercises/{$exercise->id}");

            $response->assertOk();
            $response->assertJsonPath('data.id', $exercise->id);
            $response->assertJsonPath('data.name', $exercise->name);
        });

        test('user cannot view another user\'s exercise', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $exercise = Exercise::factory()->for($user1)->create();

            $response = $this->actingAs($user2)->getJson("/api/exercises/{$exercise->id}");

            $response->assertForbidden();
        });

        test('exercise not found returns 404', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->getJson('/api/exercises/99999');

            $response->assertNotFound();
        });
    });

    describe('store', function () {
        test('authenticated user can create exercise', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson('/api/exercises', [
                'name' => 'Bench Press',
                'muscleGroup' => 'Chest',
                'description' => 'Upper body push exercise',
            ]);

            $response->assertCreated();
            $this->assertDatabaseHas('exercises', [
                'name' => 'Bench Press',
                'user_id' => $user->id,
            ]);
        });

        test('create exercise validates required fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson('/api/exercises', []);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['name', 'muscleGroup']);
        });

        test('unauthenticated user cannot create exercise', function () {
            $response = $this->postJson('/api/exercises', [
                'name' => 'Bench Press',
                'muscleGroup' => 'Chest',
            ]);

            $response->assertUnauthorized();
        });
    });

    describe('update', function () {
        test('user can update their own exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create([
                'name' => 'Bench Press',
                'muscleGroup' => 'Chest',
            ]);

            $response = $this->actingAs($user)->putJson("/api/exercises/{$exercise->id}", [
                'name' => 'Incline Bench Press',
                'muscleGroup' => 'Upper Chest',
            ]);

            $response->assertOk();
            $this->assertDatabaseHas('exercises', [
                'id' => $exercise->id,
                'name' => 'Incline Bench Press',
            ]);
        });

        test('user cannot update another user\'s exercise', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $exercise = Exercise::factory()->for($user1)->create();

            $response = $this->actingAs($user2)->putJson("/api/exercises/{$exercise->id}", [
                'name' => 'Updated Name',
                'muscleGroup' => 'Updated Group',
            ]);

            $response->assertForbidden();
        });
    });

    describe('destroy', function () {
        test('user can delete their own exercise', function () {
            $user = User::factory()->create();
            $exercise = Exercise::factory()->for($user)->create();

            $response = $this->actingAs($user)->deleteJson("/api/exercises/{$exercise->id}");

            $response->assertOk();
            $this->assertDatabaseMissing('exercises', ['id' => $exercise->id]);
        });

        test('user cannot delete another user\'s exercise', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $exercise = Exercise::factory()->for($user1)->create();

            $response = $this->actingAs($user2)->deleteJson("/api/exercises/{$exercise->id}");

            $response->assertForbidden();
            $this->assertDatabaseHas('exercises', ['id' => $exercise->id]);
        });
    });

    describe('muscleGroups', function () {
        test('user can get their unique muscle groups', function () {
            $user = User::factory()->create();
            Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);
            Exercise::factory()->for($user)->create(['muscleGroup' => 'Back']);
            Exercise::factory()->for($user)->create(['muscleGroup' => 'Chest']);

            $response = $this->actingAs($user)->getJson('/api/exercises/muscle-groups');

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data']);
        });

        test('unauthenticated user cannot get muscle groups', function () {
            $response = $this->getJson('/api/exercises/muscle-groups');

            $response->assertUnauthorized();
        });
    });

    describe('rate limiting on write operations', function () {
        test('create exercise is rate limited to 30 requests per minute', function () {
            $user = User::factory()->create();

            // Make 30 successful requests
            for ($i = 0; $i < 30; $i++) {
                $response = $this->actingAs($user)->postJson('/api/exercises', [
                    'name' => "Exercise $i",
                    'muscleGroup' => 'Chest',
                ]);
                $this->assertNotEquals(429, $response->status());
            }

            // 31st request should be rate limited
            $response = $this->actingAs($user)->postJson('/api/exercises', [
                'name' => 'Exercise 31',
                'muscleGroup' => 'Chest',
            ]);
            $this->assertEquals(429, $response->status());
        });
    });
});
