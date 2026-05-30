<?php

namespace Tests\Feature;

use App\Enums\SubscriptionTierType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test updating subscription tier to pro
     */
    public function test_update_subscription_to_pro(): void
    {
        $user = User::factory()->create(['subscription_tier' => SubscriptionTierType::FREE]);

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/user/subscription', [
                'tier' => SubscriptionTierType::PRO->value,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(SubscriptionTierType::PRO, $user->fresh()->subscription_tier);
    }

    /**
     * Test updating subscription tier to pro_plus
     */
    public function test_update_subscription_to_pro_plus(): void
    {
        $user = User::factory()->create(['subscription_tier' => SubscriptionTierType::FREE]);

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/user/subscription', [
                'tier' => SubscriptionTierType::PRO_PLUS->value,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(SubscriptionTierType::PRO_PLUS, $user->fresh()->subscription_tier);
    }

    /**
     * Test downgrading subscription to free
     */
    public function test_downgrade_subscription_to_free(): void
    {
        $user = User::factory()->create(['subscription_tier' => SubscriptionTierType::PRO]);

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/user/subscription', [
                'tier' => SubscriptionTierType::FREE->value,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(SubscriptionTierType::FREE, $user->fresh()->subscription_tier);
    }

    /**
     * Test validation fails with invalid tier
     */
    public function test_validation_fails_with_invalid_tier(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/user/subscription', [
                'tier' => 'invalid_tier',
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test validation fails when tier is missing
     */
    public function test_validation_fails_when_tier_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/user/subscription', []);

        $response->assertStatus(422);
    }

    /**
     * Test unauthenticated users cannot update subscription
     */
    public function test_unauthenticated_users_cannot_update_subscription(): void
    {
        $response = $this->patchJson('/api/user/subscription', [
            'tier' => SubscriptionTierType::PRO->value,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test response includes subscription_tier
     */
    public function test_response_includes_subscription_tier(): void
    {
        $user = User::factory()->create(['subscription_tier' => SubscriptionTierType::FREE]);

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/user/subscription', [
                'tier' => SubscriptionTierType::PRO->value,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('attributes.subscription_tier', 'pro');
    }
}
