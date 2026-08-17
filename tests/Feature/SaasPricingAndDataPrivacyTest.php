<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SaasPricingAndDataPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_page_is_publicly_accessible(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
        $response->assertSee('Pilihan Paket PortoFinance PRO');
        $response->assertSee('Free Starter');
        $response->assertSee('PortoFinance PRO');
        $response->assertSee('Lifetime VIP Pass');
    }

    public function test_invoice_whatsapp_share_url_and_watermark(): void
    {
        $freeUser = User::factory()->create([
            'role' => 'user',
            'subscription_tier' => 'free',
            'trial_ends_at' => null,
            'email_verified_at' => now(),
        ]);

        $client = Client::create([
            'user_id' => $freeUser->id,
            'name' => 'PT Klien Maju',
            'email' => 'klien@maju.com',
            'phone' => '08123456789',
        ]);

        $project = Project::create([
            'user_id' => $freeUser->id,
            'client_id' => $client->id,
            'name' => 'Video Branding',
            'total_revenue' => 5000000,
            'status' => 'in_progress',
        ]);

        $invoice = Invoice::create([
            'project_id' => $project->id,
            'invoice_number' => 'INV-2026-001',
            'amount' => 5000000,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'sent',
        ]);

        $this->assertNotEmpty($invoice->whatsapp_share_url);
        $this->assertStringContainsString('628123456789', $invoice->whatsapp_share_url);
        $this->assertStringContainsString('INV-2026-001', $invoice->whatsapp_share_url);

        // View public invoice: should display watermark for free user
        $response = $this->get($invoice->public_url);
        $response->assertStatus(200);
        $response->assertSee('Dibuat dengan');
        $response->assertSee('PortoFinance');
    }

    public function test_user_can_export_full_data_backup(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Bisnis',
            'type' => 'bank',
            'initial_balance' => 1000000,
            'current_balance' => 1000000,
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Settings\Index::class)
            ->call('exportAllData')
            ->assertFileDownloaded();
    }

    public function test_user_can_permanently_delete_their_account(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $userId = $user->id;
        $this->actingAs($user);

        Livewire::test(\App\Livewire\Settings\Index::class)
            ->set('delete_password', 'password123')
            ->call('deleteAccount')
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
