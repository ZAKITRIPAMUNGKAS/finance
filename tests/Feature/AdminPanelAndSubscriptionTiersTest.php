<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelAndSubscriptionTiersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_gets_forbidden_on_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'trial',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard_and_users(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'subscription_tier' => 'lifetime',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Panel Kontrol', false);

        $usersResponse = $this->actingAs($admin)->get('/admin/users');
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('Data', false);
    }

    public function test_admin_can_upgrade_user_subscription_tier(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'subscription_tier' => 'lifetime',
            'email_verified_at' => now(),
        ]);

        $regularUser = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'free',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Users\Index::class)
            ->call('setTierDirect', $regularUser->id, 'pro', 30)
            ->assertHasNoErrors();

        $regularUser->refresh();
        $this->assertEquals('pro', $regularUser->subscription_tier);
        $this->assertNotNull($regularUser->subscription_ends_at);
        $this->assertTrue($regularUser->isPro());
    }

    public function test_admin_can_ban_and_unban_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'subscription_tier' => 'lifetime',
            'email_verified_at' => now(),
        ]);

        $targetUser = User::factory()->create([
            'role' => 'user',
            'is_banned' => false,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Users\Index::class)
            ->call('toggleBan', $targetUser->id)
            ->assertHasNoErrors();

        $targetUser->refresh();
        $this->assertTrue((bool)$targetUser->is_banned);

        // Banned user cannot access application
        $bannedAccess = $this->actingAs($targetUser)->get('/dashboard');
        $bannedAccess->assertRedirect('/login');
    }

    public function test_admin_can_set_user_financial_persona(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'subscription_tier' => 'lifetime',
            'email_verified_at' => now(),
        ]);

        $targetUser = User::factory()->create([
            'role' => 'user',
            'financial_persona' => 'freelancer',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Users\Index::class)
            ->call('setPersonaDirect', $targetUser->id, 'student', app(\App\Services\BudgetAllocationService::class))
            ->assertHasNoErrors();

        $targetUser->refresh();
        $this->assertEquals('student', $targetUser->financial_persona);
        $this->assertTrue($targetUser->isStudent());

        // Test editing persona via modal
        Livewire::test(\App\Livewire\Admin\Users\Index::class)
            ->call('openEditModal', $targetUser->id)
            ->set('editPersona', 'merchant')
            ->call('saveUserChanges', app(\App\Services\BudgetAllocationService::class))
            ->assertHasNoErrors();

        $targetUser->refresh();
        $this->assertEquals('merchant', $targetUser->financial_persona);
        $this->assertTrue($targetUser->isMerchant());
    }
}
