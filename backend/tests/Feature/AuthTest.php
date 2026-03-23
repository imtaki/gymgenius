<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Authentication', function () {
    describe('register', function () {
        test('user can register with valid data', function () {
            $response = $this->postJson('/api/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'Password123!',
            ]);

            $response->assertCreated();
            $response->assertJsonStructure(['success', 'data' => ['token', 'user']]);
            $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        });

        test('register validates required fields', function () {
            $response = $this->postJson('/api/register', []);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['name', 'email', 'password']);
        });

        test('register validates email format', function () {
            $response = $this->postJson('/api/register', [
                'name' => 'John',
                'email' => 'invalid-email',
                'password' => 'Password123!',
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['email']);
        });

        test('register prevents duplicate emails', function () {
            $existingUser = User::factory()->create(['email' => 'john@example.com']);

            $response = $this->postJson('/api/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'Password123!',
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['email']);
        });

        test('register creates user settings on user creation', function () {
            $this->postJson('/api/register', [
                'name' => 'John',
                'email' => 'john@example.com',
                'password' => 'Password123!',
            ]);

            $user = User::where('email', 'john@example.com')->first();
            $this->assertNotNull($user->settings);
        });
    });

    describe('login', function () {
        test('user can login with valid credentials', function () {
            $user = User::factory()->create([
                'email' => 'john@example.com',
                'password' => bcrypt('Password123!'),
                'is_verified' => true,
            ]);

            $response = $this->postJson('/api/login', [
                'email' => 'john@example.com',
                'password' => 'Password123!',
            ]);

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data' => ['token', 'user']]);
        });

        test('login fails with invalid password', function () {
            User::factory()->create([
                'email' => 'john@example.com',
                'password' => bcrypt('Password123!'),
            ]);

            $response = $this->postJson('/api/login', [
                'email' => 'john@example.com',
                'password' => 'WrongPassword!',
            ]);

            $response->assertUnauthorized();
        });

        test('login fails with non-existent email', function () {
            $response = $this->postJson('/api/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'Password123!',
            ]);

            $response->assertUnauthorized();
        });

        test('login validates required fields', function () {
            $response = $this->postJson('/api/login', []);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(['email', 'password']);
        });
    });

    describe('logout', function () {
        test('authenticated user can logout', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->postJson('/api/auth/logout');

            $response->assertOk();
        });

        test('unauthenticated user cannot logout', function () {
            $response = $this->postJson('/api/auth/logout');

            $response->assertUnauthorized();
        });
    });

    describe('getUser', function () {
        test('authenticated user can get their profile', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->getJson('/api/auth/user');

            $response->assertOk();
            $response->assertJsonStructure(['success', 'data' => ['id', 'name', 'email', 'role']]);
            $response->assertJsonPath('data.id', $user->id);
        });

        test('unauthenticated user cannot access user endpoint', function () {
            $response = $this->getJson('/api/auth/user');

            $response->assertUnauthorized();
        });
    });

    describe('rate limiting on auth endpoints', function () {
        test('login is rate limited to 5 requests per minute per IP', function () {
            for ($i = 0; $i < 5; $i++) {
                $response = $this->postJson('/api/login', [
                    'email' => 'nonexistent@example.com',
                    'password' => 'test',
                ]);
                $this->assertNotEquals(429, $response->status());
            }

            // 6th request should be rate limited
            $response = $this->postJson('/api/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'test',
            ]);
            $this->assertEquals(429, $response->status());
        });

        test('register is rate limited to 5 requests per minute per IP', function () {
            for ($i = 0; $i < 5; $i++) {
                $this->postJson('/api/register', [
                    'name' => "User $i",
                    'email' => "user$i@example.com",
                    'password' => 'Password123!',
                ]);
            }

            // 6th request should be rate limited
            $response = $this->postJson('/api/register', [
                'name' => 'User 6',
                'email' => 'user6@example.com',
                'password' => 'Password123!',
            ]);
            $this->assertEquals(429, $response->status());
        });
    });
});
