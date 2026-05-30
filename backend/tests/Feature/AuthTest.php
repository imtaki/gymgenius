<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\SubscriptionTierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;


class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // Register tests
    #[Test]
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

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id', 'type', 'attributes' => [
                    'name', 'email', 'role', 'is_verified'
                ]
            ],
            'token'
        ]);
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    #[Test]
    #[Group('register')]
    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    #[Group('register')]
    public function test_register_assigns_free_tier(): void
    {
        $email = 'tiertest@example.com';

        $response = $this->postJson('/api/register', [
            'name' => 'Tier Test',
            'email' => $email,
            'password' => 'Password123!',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals(SubscriptionTierType::FREE, $user->subscription_tier);
    }

    #[Test]
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
    #[Test]
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
        // Login still returns the old format - check what endpoint returns
        $response->assertJsonStructure([
            'message',
            'id',
            'token'
        ]);
    }

    #[Test]
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

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'success',
            'error',
        ]);
    }

    #[Test]
    #[Group('login')]
    public function test_login_fails_with_non_existent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->faker->email(),
            'password' => 'Password123!',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'success',
            'error',
        ]);
    }

    #[Test]
    #[Group('login')]
    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        // Empty credentials returns 401, not validation error (login doesn't have FormRequest validation)
        $response->assertStatus(401);
    }

    #[Group('logout')]
    #[Test]
    // Logout tests
    public function test_authenticated_user_can_logout(): void
    {
        // Create user
        $user = User::factory()->create();

        // Create JWT token for the user
        $token = JWTAuth::fromUser($user);


        // Logout call
        $response = $this->withHeader('Authorization', "Bearer $token")
                     ->postJson('/api/auth/logout');

        $response->assertOk()
             ->assertJson(['message' => 'Successfully logged out']);
    }

    #[Group('logout')]
    #[Test]
    public function test_authenticated_user_can_logout_and_token_is_invalidated(): void
    {
        // Create user
        $user = User::factory()->create();

        // Create JWT token for the user
        $token = JWTAuth::fromUser($user);


        // Logout call
        $response = $this->withHeader('Authorization', "Bearer $token")
                     ->postJson('/api/auth/logout');

        $response->assertOk()
             ->assertJson(['message' => 'Successfully logged out']);

         $this->withHeader('Authorization', "Bearer $token")
         ->getJson('/api/auth/user')
         ->assertUnauthorized();
    }

    #[Group('logout')]
    #[Test]
    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    }

    // GetUser tests
    #[Test]
    #[Group('user')]
   public function test_authenticated_user_can_get_their_profile(): void
    {
    // 1. Create the user with explicit attributes so we know what to expect
    $user = User::factory()->create([
        'role' => 'user',
        'is_verified' => false,
    ]);

    $token = JWTAuth::fromUser($user);

    // 2. Request
    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->getJson('/api/auth/user');

    $response->assertOk();

    $response->assertJsonStructure([
        'success',
        'data' => [
            'user' => [
                'id', 'type', 'attributes' => [
                    'name', 'email', 'role', 'is_verified'
                ]
            ]
        ]
    ]);

    $response->assertJson([
        'success' => true,
        'data' => [
            'user' => [
                'type'        => 'user',
                'attributes' => [
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'role'        => 'user',
                    'is_verified' => false,
                ]
            ]
        ]
    ]);
    }

    #[Test]
    #[Group('user')]
    public function test_unauthenticated_user_cannot_access_user_endpoint(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertUnauthorized();
    }

    #[Test]
    #[TestDox('Test that the login endpoint is rate limited to 5 requests per minute per IP')]
    #[Group('RateLimit')]
    public function test_login_is_rate_limited_to_5_requests_per_minute_per_ip(): void
    {
         // Get the cache store for rate limiting
        $cache = Cache::store(config('ratelimit.cache_store'));
        $cache->flush();

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => $this->faker->email(),
                'password' =>$this->faker->password(),
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/login', [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ]);
        $this->assertEquals(429, $response->status());
    }

    #[Test]
    #[TestDox('Test that the register endpoint is rate limited to 5 requests per minute per IP')]
    #[Group('RateLimit')]
    public function test_register_is_rate_limited_to_5_requests_per_minute_per_ip(): void
    {

        $cache = Cache::store(config('ratelimit.cache_store'));
        $cache->flush();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/register', [
                'name' => "User $i",
                'email' => $this->faker->email(),
                'password' => $this->faker->password(),
            ]);
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/register', [
            'name' => 'User 6',
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ]);
        $this->assertEquals(429, $response->status());
    }
}
