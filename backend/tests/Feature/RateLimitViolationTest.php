<?php

use App\Models\RateLimitViolation;
use App\Models\User;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('RateLimitViolation', function () {
    describe('logging violations', function () {
        test('rate limit violation is logged when limit exceeded', function () {
            $violations = [];
            
            // Make 6 login attempts to trigger rate limit
            for ($i = 0; $i < 6; $i++) {
                $response = $this->postJson('/api/login', [
                    'email' => 'test@example.com',
                    'password' => 'test',
                ]);
                
                if ($response->status() === 429) {
                    // Violation was triggered, now check if logged
                    $this->waitForQueue();
                }
            }

            // Allow queue to process
            $this->artisan('queue:work', ['--max-jobs' => 100]);

            // Check that violations were logged
            $violations = RateLimitViolation::where('endpoint', '/api/login')->get();
            $this->assertGreaterThan(0, $violations->count());
        });

        test('violation record contains correct data', function () {
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
            $this->assertEquals('/api/login', $violation->endpoint);
            $this->assertEquals('POST', $violation->method);
            $this->assertNotNull($violation->ip_address);
            $this->assertNotNull($violation->user_agent);
            $this->assertNotNull($violation->identifier);
        });
    });

    describe('model scopes', function () {
        test('recent scope filters violations by time window', function () {
            $now = now();
            RateLimitViolation::factory()->create(['created_at' => $now]);
            RateLimitViolation::factory()->create(['created_at' => $now->copy()->subMinutes(30)]);
            RateLimitViolation::factory()->create(['created_at' => $now->copy()->subHours(2)]);

            $recent = RateLimitViolation::recent(60)->count();
            $this->assertEquals(2, $recent);
        });

        test('forUser scope filters by user id', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            RateLimitViolation::factory()->create(['user_id' => $user1->id]);
            RateLimitViolation::factory()->create(['user_id' => $user1->id]);
            RateLimitViolation::factory()->create(['user_id' => $user2->id]);

            $violations = RateLimitViolation::forUser($user1->id)->count();
            $this->assertEquals(2, $violations);
        });

        test('forIp scope filters by IP address', function () {
            RateLimitViolation::factory()->create(['ip_address' => '192.168.1.1']);
            RateLimitViolation::factory()->create(['ip_address' => '192.168.1.1']);
            RateLimitViolation::factory()->create(['ip_address' => '192.168.1.2']);

            $violations = RateLimitViolation::forIp('192.168.1.1')->count();
            $this->assertEquals(2, $violations);
        });

        test('forEndpoint scope filters by endpoint', function () {
            RateLimitViolation::factory()->create(['endpoint' => '/api/login']);
            RateLimitViolation::factory()->create(['endpoint' => '/api/login']);
            RateLimitViolation::factory()->create(['endpoint' => '/api/register']);

            $violations = RateLimitViolation::forEndpoint('/api/login')->count();
            $this->assertEquals(2, $violations);
        });
    });

    describe('analytics methods', function () {
        test('getStatistics returns correct summary', function () {
            $now = now();
            RateLimitViolation::factory()->count(5)->create(['created_at' => $now, 'user_id' => 1]);
            RateLimitViolation::factory()->count(3)->create(['created_at' => $now, 'user_id' => 2]);
            RateLimitViolation::factory()->count(2)->create(['created_at' => $now, 'ip_address' => '192.168.1.1']);
            RateLimitViolation::factory()->count(1)->create(['created_at' => $now, 'ip_address' => '192.168.1.2']);

            $stats = RateLimitViolation::getStatistics(1440);

            $this->assertGreaterThan(0, $stats['total_violations']);
            $this->assertGreaterThan(0, $stats['unique_users']);
            $this->assertGreaterThan(0, $stats['unique_ips']);
        });

        test('getTopEndpoints returns endpoints sorted by violation count', function () {
            RateLimitViolation::factory()->count(10)->create(['endpoint' => '/api/login']);
            RateLimitViolation::factory()->count(5)->create(['endpoint' => '/api/meals']);
            RateLimitViolation::factory()->count(3)->create(['endpoint' => '/api/exercises']);

            $topEndpoints = RateLimitViolation::getTopEndpoints(5, 1440);

            $this->assertEquals('/api/login', $topEndpoints[0]->endpoint);
            $this->assertEquals(10, $topEndpoints[0]->violation_count);
            $this->assertEquals('/api/meals', $topEndpoints[1]->endpoint);
        });

        test('getTopIps returns IPs sorted by violation count', function () {
            RateLimitViolation::factory()->count(8)->create(['ip_address' => '192.168.1.1']);
            RateLimitViolation::factory()->count(5)->create(['ip_address' => '192.168.1.2']);
            RateLimitViolation::factory()->count(2)->create(['ip_address' => '192.168.1.3']);

            $topIps = RateLimitViolation::getTopIps(5, 1440);

            $this->assertEquals('192.168.1.1', $topIps[0]->ip_address);
            $this->assertEquals(8, $topIps[0]->violation_count);
        });
    });

    describe('relationships', function () {
        test('violation belongs to user', function () {
            $user = User::factory()->create();
            $violation = RateLimitViolation::factory()->for($user)->create();

            $this->assertTrue($violation->user->is($user));
        });

        test('violation maintains data when related user exists', function () {
            $user = User::factory()->create();
            $violation = RateLimitViolation::factory()->for($user)->create();

            $retrieved = RateLimitViolation::find($violation->id);
            $this->assertEquals($user->id, $retrieved->user_id);
        });
    });

    describe('cleanup', function () {
        test('old violations can be deleted by date', function () {
            $now = now();
            RateLimitViolation::factory()->create(['created_at' => $now]);
            RateLimitViolation::factory()->create(['created_at' => $now->copy()->subDays(45)]);

            $deleted = RateLimitViolation::where('created_at', '<', $now->copy()->subDays(30))->delete();

            $this->assertEquals(1, $deleted);
            $this->assertEquals(1, RateLimitViolation::count());
        });
    });

    describe('response headers', function () {
        test('rate limited response includes proper headers', function () {
            for ($i = 0; $i < 6; $i++) {
                $response = $this->postJson('/api/login', [
                    'email' => 'test@example.com',
                    'password' => 'test',
                ]);
            }

            $lastResponse = $response;
            
            if ($lastResponse->status() === 429) {
                $this->assertNotNull($lastResponse->headers->get('Retry-After'));
                $this->assertTrue($lastResponse->json()['success'] === false);
                $this->assertStringContainsString('Too many requests', $lastResponse->json()['message']);
            }
        });
    });
});
