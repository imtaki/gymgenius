<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\WorkoutSplitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

class WorkoutSplitServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkoutSplitService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(WorkoutSplitService::class);
    }

    #[Test]
    #[Group('service')]
    public function test_create_and_retrieve_user_splits(): void
    {
        $user = User::factory()->create();

        $data = new \App\Data\CreateWorkoutSplitData(
            'Service Split',
            'created via service'
        );

        $split = $this->service->create($user->id, $data);
        $this->assertNotNull($split->id);

        $owned = $this->service->paginate($user->id, 50);
        $this->assertGreaterThanOrEqual(1, $owned->total());
    }
}
