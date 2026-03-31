<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;


class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // Register tests
    #[Group('register')]
    public function test_user_can_register_with_valid_data(): void
    {
        // Generate mock data for registration
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = strtolower($firstName . '.' . $lastName . '@mailinator.com');

        $response = $this->postJson('/api/register', [
            'name' => "{$firstName} {$lastName}",
            'email' => $email,
            'password' => 'Password123!',
        ]);

        $response->dump();
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id', 'name', 'email', 'role', 'created_at', 'updated_at'
            ],
            'token'
        ]);
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    #[Group('register')]
    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    #[Group('register')]
    public function test_register_validates_email_format(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'invalid-email',
            'password' => 'Password123!',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }

    #[Group('register')]
    public function test_register_prevents_duplicate_emails(): void
    {
        $existingUser = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }

    #[Group('register')]
    public function test_register_creates_user_settings_on_user_creation(): void
    {
        $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->settings);
    }

    // Login tests
    #[Group('login')]
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('Password123!'),
            'is_verified' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'id',
            'token'
        ]);
    }

    #[Group('login')]
    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => $this->faker->email(),
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $this->faker->email(),
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(400);
    }

    #[Group('login')]
    public function test_login_fails_with_non_existent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertUnauthorized();
    }

    #[Group('login')]
    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /** @group logout */
    // Logout tests
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/auth/logout');

        $response->assertOk();
    }
    /** @group logout */
    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    }

    // GetUser tests
    public function test_authenticated_user_can_get_their_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/auth/user');

        $response->assertOk();
        $response->assertJsonStructure(['success', 'data' => ['id', 'name', 'email', 'role']]);
        $response->assertJsonPath('data.id', $user->id);
    }

    public function test_unauthenticated_user_cannot_access_user_endpoint(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertUnauthorized();
    }

    // Rate limiting tests
    public function test_login_is_rate_limited_to_5_requests_per_minute_per_ip(): void
    {
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
    }

    public function test_register_is_rate_limited_to_5_requests_per_minute_per_ip(): void
    {
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
    }
}
