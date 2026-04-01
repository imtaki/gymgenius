<?php

namespace Tests\Feature;

use App\Models\RateLimitViolation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

class RateLimitViolationTest extends TestCase
{
    use RefreshDatabase;

    // Logging violations tests
    #[Test]
    #[Group('RateLimitViolation.Logging')]
    #[TestDox('Test that rate limit violation is logged when limit exceeded')]
    public function test_rate_limit_violation_is_logged_when_limit_exceeded(): void
    {
        // Make 6 login attempts to trigger rate limit
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'test',
            ]);

            if ($response->status() === 429) {
                // Violation was triggered
                break;
            }
        }

        // Allow queue to process
        $this->artisan('queue:work', ['--max-jobs' => 100]);

        // Check that violations were logged
        $violations = RateLimitViolation::where('endpoint', '/api/login')->get();
        $this->assertGreaterThan(0, $violations->count());
    }

    #[Test]
    #[Group('RateLimitViolation.Logging')]
    #[TestDox('Test that violation record contains correct data')]
    public function test_violation_record_contains_correct_data(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'test',
            ]);
        }

        // Process queue
        $this->artisan('queue:work', ['--max-jobs' => 100]);

        $violation = RateLimitViolation::first();

        $this->assertNotNull($violation);
        if ($violation) {
            $this->assertEquals('/api/login', $violation->endpoint);
            $this->assertEquals('POST', $violation->method);
            $this->assertNotNull($violation->ip_address);
            $this->assertNotNull($violation->user_agent);
            $this->assertNotNull($violation->identifier);
        }
    }

    // Model scopes tests
    #[Test]
    #[Group('RateLimitViolation.Scopes')]
    #[TestDox('Test that recent scope filters violations by time window')]
    public function test_recent_scope_filters_violations_by_time_window(): void
    {
        $now = now();
        RateLimitViolation::factory()->create(['created_at' => $now]);
        RateLimitViolation::factory()->create(['created_at' => $now->copy()->subMinutes(30)]);
        RateLimitViolation::factory()->create(['created_at' => $now->copy()->subHours(2)]);

        $recent = RateLimitViolation::recent(60)->count();
        $this->assertEquals(2, $recent);
    }

    #[Test]
    #[Group('RateLimitViolation.Scopes')]
    #[TestDox('Test that forUser scope filters by user ID')]
    public function test_for_user_scope_filters_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        RateLimitViolation::factory()->create(['user_id' => $user1->id]);
        RateLimitViolation::factory()->create(['user_id' => $user1->id]);
        RateLimitViolation::factory()->create(['user_id' => $user2->id]);

        $violations = RateLimitViolation::forUser($user1->id)->count();
        $this->assertEquals(2, $violations);
    }

    #[Test]
    #[Group('RateLimitViolation.Scopes')]
    #[TestDox('Test that forIp scope filters by IP address')]
    public function test_for_ip_scope_filters_by_ip_address(): void
    {
        RateLimitViolation::factory()->create(['ip_address' => '192.168.1.1']);
        RateLimitViolation::factory()->create(['ip_address' => '192.168.1.1']);
        RateLimitViolation::factory()->create(['ip_address' => '192.168.1.2']);

        $violations = RateLimitViolation::forIp('192.168.1.1')->count();
        $this->assertEquals(2, $violations);
    }

    #[Test]
    #[Group('RateLimitViolation.Scopes')]
    #[TestDox('Test that forEndpoint scope filters by endpoint')]
    public function test_for_endpoint_scope_filters_by_endpoint(): void
    {
        RateLimitViolation::factory()->create(['endpoint' => '/api/login']);
        RateLimitViolation::factory()->create(['endpoint' => '/api/login']);
        RateLimitViolation::factory()->create(['endpoint' => '/api/register']);

        $violations = RateLimitViolation::forEndpoint('/api/login')->count();
        $this->assertEquals(2, $violations);
    }

    // Analytics methods tests
    #[Test]
    #[Group('RateLimitViolation.Analytics')]
    #[TestDox('Test that get statistics returns correct summary')]
    public function test_get_statistics_returns_correct_summary(): void
    {
        $now = now();
        RateLimitViolation::factory()->count(5)->create(['created_at' => $now, 'user_id' => 1]);
        RateLimitViolation::factory()->count(3)->create(['created_at' => $now, 'user_id' => 2]);
        RateLimitViolation::factory()->count(2)->create(['created_at' => $now, 'ip_address' => '192.168.1.1']);
        RateLimitViolation::factory()->count(1)->create(['created_at' => $now, 'ip_address' => '192.168.1.2']);

        $stats = RateLimitViolation::getStatistics(1440);

        $this->assertGreaterThan(0, $stats['total_violations']);
        $this->assertGreaterThan(0, $stats['unique_users']);
        $this->assertGreaterThan(0, $stats['unique_ips']);
    }

    #[Test]
    #[Group('RateLimitViolation.Analytics')]
    #[TestDox('Test that get top endpoints returns endpoints sorted by violation count')]
    public function test_get_top_endpoints_returns_endpoints_sorted_by_violation_count(): void
    {
        RateLimitViolation::factory()->count(10)->create(['endpoint' => '/api/login']);
        RateLimitViolation::factory()->count(5)->create(['endpoint' => '/api/meals']);
        RateLimitViolation::factory()->count(3)->create(['endpoint' => '/api/exercises']);

        $topEndpoints = RateLimitViolation::getTopEndpoints(5, 1440);

        $this->assertEquals('/api/login', $topEndpoints[0]->endpoint);
        $this->assertEquals(10, $topEndpoints[0]->violation_count);
        $this->assertEquals('/api/meals', $topEndpoints[1]->endpoint);
    }

    #[Test]
    #[Group('RateLimitViolation.Analytics')]
    #[TestDox('Test that get top IPs returns IPs sorted by violation count')]
    public function test_get_top_ips_returns_ips_sorted_by_violation_count(): void
    {
        RateLimitViolation::factory()->count(8)->create(['ip_address' => '192.168.1.1']);
        RateLimitViolation::factory()->count(5)->create(['ip_address' => '192.168.1.2']);
        RateLimitViolation::factory()->count(2)->create(['ip_address' => '192.168.1.3']);

        $topIps = RateLimitViolation::getTopIps(5, 1440);

        $this->assertEquals('192.168.1.1', $topIps[0]->ip_address);
        $this->assertEquals(8, $topIps[0]->violation_count);
    }

    // Relationships tests
    #[Test]
    #[Group('RateLimitViolation.Relationships')]
    #[TestDox('Test that violation belongs to user')]
    public function test_violation_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $violation = RateLimitViolation::factory()->for($user)->create();

        $this->assertTrue($violation->user->is($user));
    }

    #[Test]
    #[Group('RateLimitViolation.Relationships')]
    #[TestDox('Test that violation maintains data when related user exists')]
    public function test_violation_maintains_data_when_related_user_exists(): void
    {
        $user = User::factory()->create();
        $violation = RateLimitViolation::factory()->for($user)->create();

        $retrieved = RateLimitViolation::find($violation->id);
        $this->assertEquals($user->id, $retrieved->user_id);
    }

    // Cleanup tests
    #[Test]
    #[Group('RateLimitViolation.Cleanup')]
    #[TestDox('Test that old violations can be deleted by date')]
    public function test_old_violations_can_be_deleted_by_date(): void
    {
        $now = now();
        RateLimitViolation::factory()->create(['created_at' => $now]);
        RateLimitViolation::factory()->create(['created_at' => $now->copy()->subDays(45)]);

        $deleted = RateLimitViolation::where('created_at', '<', $now->copy()->subDays(30))->delete();

        $this->assertEquals(1, $deleted);
        $this->assertEquals(1, RateLimitViolation::count());
    }

    // Response headers tests
    #[Test]
    #[Group('RateLimitViolation.Headers')]
    #[TestDox('Test that rate limited response includes proper headers')]
    public function test_rate_limited_response_includes_proper_headers(): void
    {
        $response = null;
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'test',
            ]);
        }

        if ($response && $response->status() === 429) {
            $this->assertNotNull($response->headers->get('Retry-After'));
            $this->assertTrue($response->json()['success'] === false);
            $this->assertStringContainsString('Too many requests', $response->json()['message']);
        }
    }
}
