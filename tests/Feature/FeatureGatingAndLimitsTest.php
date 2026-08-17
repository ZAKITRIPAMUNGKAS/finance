<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeatureGatingAndLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_user_account_creation_limit(): void
    {
        $freeUser = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'free',
            'trial_ends_at' => null,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($freeUser);

        // Create 2 accounts (should hit limit)
        Account::create([
            'user_id' => $freeUser->id,
            'name' => 'BCA',
            'type' => 'bank',
            'initial_balance' => 100000,
            'current_balance' => 100000,
        ]);
        Account::create([
            'user_id' => $freeUser->id,
            'name' => 'Mandiri',
            'type' => 'bank',
            'initial_balance' => 200000,
            'current_balance' => 200000,
        ]);

        $this->assertFalse($freeUser->canCreateAccount());

        // Attempting to create 3rd account via Livewire should dispatch upgrade modal
        Livewire::test(\App\Livewire\Accounts\Index::class)
            ->call('openCreateModal')
            ->assertDispatched('open-upgrade-modal');
    }

    public function test_free_user_project_creation_limit(): void
    {
        $freeUser = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'free',
            'trial_ends_at' => null,
            'email_verified_at' => now(),
        ]);

        $client = Client::create([
            'user_id' => $freeUser->id,
            'name' => 'Klien Test',
            'email' => 'klien@test.com',
        ]);
        $this->actingAs($freeUser);

        // Create 2 projects (should hit limit)
        Project::create([
            'user_id' => $freeUser->id,
            'client_id' => $client->id,
            'name' => 'Project 1',
            'total_revenue' => 1000000,
            'status' => 'in_progress',
        ]);
        Project::create([
            'user_id' => $freeUser->id,
            'client_id' => $client->id,
            'name' => 'Project 2',
            'total_revenue' => 2000000,
            'status' => 'in_progress',
        ]);

        $this->assertFalse($freeUser->canCreateProject());

        // Attempting to open project creation modal should dispatch upgrade modal
        Livewire::test(\App\Livewire\Projects\Index::class)
            ->call('openCreateProjectModal')
            ->assertDispatched('open-upgrade-modal');
    }

    public function test_free_user_ai_voice_and_ocr_monthly_quota(): void
    {
        $freeUser = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'free',
            'trial_ends_at' => null,
            'email_verified_at' => now(),
            'monthly_ai_usage' => 5,
            'ai_usage_reset_at' => now(),
        ]);

        $this->actingAs($freeUser);

        $this->assertFalse($freeUser->canUseAiVoiceOrScan());
        $this->assertEquals(0, $freeUser->remaining_ai_scans);

        // Attempting voice transaction should trigger paywall modal
        Livewire::test(\App\Livewire\QuickTransactionModal::class)
            ->call('processVoiceInput', 'Beli kopi 25rb bayar gopay')
            ->assertDispatched('open-upgrade-modal');
    }

    public function test_pro_member_has_unlimited_access(): void
    {
        $proUser = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'pro',
            'subscription_ends_at' => now()->addMonth(),
            'email_verified_at' => now(),
            'monthly_ai_usage' => 50,
            'ai_usage_reset_at' => now(),
        ]);

        $client = Client::create([
            'user_id' => $proUser->id,
            'name' => 'Klien VIP',
            'email' => 'vip@test.com',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Account::create([
                'user_id' => $proUser->id,
                'name' => "Bank $i",
                'type' => 'bank',
                'initial_balance' => 100000,
                'current_balance' => 100000,
            ]);
            Project::create([
                'user_id' => $proUser->id,
                'client_id' => $client->id,
                'name' => "Project $i",
                'total_revenue' => 1000000,
                'status' => 'in_progress',
            ]);
        }

        $this->actingAs($proUser);

        $this->assertTrue($proUser->isPro());
        $this->assertTrue($proUser->canCreateAccount());
        $this->assertTrue($proUser->canCreateProject());
        $this->assertTrue($proUser->canUseAiVoiceOrScan());
    }
}
