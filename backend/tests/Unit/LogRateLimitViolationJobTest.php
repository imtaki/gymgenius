<?php

use App\Jobs\LogRateLimitViolation;
use App\Models\RateLimitViolation;
use Illuminate\Support\Facades\Queue;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('LogRateLimitViolation Job', function () {
    describe('job execution', function () {
        test('job logs violation data to database', function () {
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
        });

        test('job can be executed and records violation', function () {
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
        });
    });

    describe('deduplication', function () {
        test('duplicate violations within 5 seconds are skipped', function () {
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
            $this->assertEquals(1, RateLimitViolation::count(), 'Duplicate violation should be skipped');
        });

        test('violations after 5 second window are logged', function () {
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
        });
    });

    describe('different endpoints', function () {
        test('same IP different endpoints are logged separately', function () {
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
        });
    });

    describe('user-based violations', function () {
        test('authenticated user violation is logged with user id', function () {
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
        });
    });
});
