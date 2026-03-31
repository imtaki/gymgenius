<?php

namespace Tests\Unit;

use App\Jobs\LogRateLimitViolation;
use App\Models\RateLimitViolation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LogRateLimitViolationJobTest extends TestCase
{
    use RefreshDatabase;

    // Job execution tests
    public function test_job_logs_violation_data_to_database(): void
    {
        Queue::fake();

        $data = [
            'user_id' => 1,
            'ip_address' => '192.168.1.1',
            'endpoint' => '/api/login',
            'method' => 'POST',
            'limit' => 5,
            'current_count' => 6,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => '192.168.1.1',
        ];

        dispatch(new LogRateLimitViolation($data));

        Queue::assertPushed(LogRateLimitViolation::class);
    }

    public function test_job_can_be_executed_and_records_violation(): void
    {
        $data = [
            'user_id' => null,
            'ip_address' => '192.168.1.1',
            'endpoint' => '/api/login',
            'method' => 'POST',
            'limit' => 5,
            'current_count' => 6,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => '192.168.1.1',
        ];

        $job = new LogRateLimitViolation($data);
        $job->handle();

        $this->assertDatabaseHas('rate_limit_violations', [
            'ip_address' => '192.168.1.1',
            'endpoint' => '/api/login',
        ]);
    }

    // Deduplication tests
    public function test_duplicate_violations_within_5_seconds_are_skipped(): void
    {
        $identifier = '192.168.1.1';
        $endpoint = '/api/login';

        $data = [
            'user_id' => null,
            'ip_address' => $identifier,
            'endpoint' => $endpoint,
            'method' => 'POST',
            'limit' => 5,
            'current_count' => 6,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => $identifier,
        ];

        // First violation
        $job1 = new LogRateLimitViolation($data);
        $job1->handle();
        $this->assertEquals(1, RateLimitViolation::count());

        // Second violation within 5 seconds - should be skipped
        $job2 = new LogRateLimitViolation($data);
        $job2->handle();
        $this->assertEquals(1, RateLimitViolation::count());
    }

    public function test_violations_after_5_second_window_are_logged(): void
    {
        $identifier = '192.168.1.1';
        $endpoint = '/api/login';

        $data = [
            'user_id' => null,
            'ip_address' => $identifier,
            'endpoint' => $endpoint,
            'method' => 'POST',
            'limit' => 5,
            'current_count' => 6,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => $identifier,
        ];

        // First violation
        $job1 = new LogRateLimitViolation($data);
        $job1->handle();
        $this->assertEquals(1, RateLimitViolation::count());

        // Manually create an old record beyond 5 seconds
        RateLimitViolation::first()->update(['created_at' => now()->subSeconds(10)]);

        // Second violation - should be logged
        $job2 = new LogRateLimitViolation($data);
        $job2->handle();
        $this->assertEquals(2, RateLimitViolation::count());
    }

    // Different endpoints tests
    public function test_same_ip_different_endpoints_are_logged_separately(): void
    {
        $ip = '192.168.1.1';

        $data1 = [
            'user_id' => null,
            'ip_address' => $ip,
            'endpoint' => '/api/login',
            'method' => 'POST',
            'limit' => 5,
            'current_count' => 6,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => $ip,
        ];

        $data2 = [
            'user_id' => null,
            'ip_address' => $ip,
            'endpoint' => '/api/register',
            'method' => 'POST',
            'limit' => 5,
            'current_count' => 6,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => $ip,
        ];

        $job1 = new LogRateLimitViolation($data1);
        $job1->handle();

        $job2 = new LogRateLimitViolation($data2);
        $job2->handle();

        $this->assertEquals(2, RateLimitViolation::count());
        $this->assertDatabaseHas('rate_limit_violations', ['endpoint' => '/api/login']);
        $this->assertDatabaseHas('rate_limit_violations', ['endpoint' => '/api/register']);
    }

    // User-based violations tests
    public function test_authenticated_user_violation_is_logged_with_user_id(): void
    {
        $userId = 1;
        $identifier = (string)$userId;

        $data = [
            'user_id' => $userId,
            'ip_address' => '192.168.1.50',
            'endpoint' => '/api/meals',
            'method' => 'POST',
            'limit' => 30,
            'current_count' => 31,
            'window_in_minutes' => 1,
            'user_agent' => 'Mozilla/5.0',
            'identifier' => $identifier,
        ];

        $job = new LogRateLimitViolation($data);
        $job->handle();

        $this->assertDatabaseHas('rate_limit_violations', [
            'user_id' => $userId,
            'identifier' => $identifier,
        ]);
    }
}
