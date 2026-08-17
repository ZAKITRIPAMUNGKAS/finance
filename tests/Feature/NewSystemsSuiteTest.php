<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AiFinancialAdvisorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewSystemsSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $account;
    protected Category $expenseCategory;
    protected Category $incomeCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $this->account = Account::create([
            'user_id' => $this->user->id,
            'name' => 'BCA Bisnis',
            'type' => 'bank',
            'current_balance' => 15000000,
            'account_number' => '1234567890',
            'is_active' => true,
        ]);

        $this->expenseCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Software & Tools',
            'type' => 'expense',
        ]);

        $this->incomeCategory = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Jasa Desain',
            'type' => 'income',
        ]);
    }

    public function test_subscriptions_page_is_accessible_and_manages_subscriptions(): void
    {
        $this->actingAs($this->user);

        // Access route
        $response = $this->get(route('subscriptions'));
        $response->assertOk();

        // Create subscription via Livewire
        Livewire::test(\App\Livewire\Subscriptions\Index::class)
            ->set('name', 'Figma Pro')
            ->set('amount', '240000')
            ->set('billing_cycle', 'monthly')
            ->set('billing_date', 10)
            ->set('account_id', $this->account->id)
            ->set('category_id', $this->expenseCategory->id)
            ->set('status', 'active')
            ->call('saveSubscription')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'name' => 'Figma Pro',
            'amount' => 240000,
        ]);

        $sub = Subscription::where('name', 'Figma Pro')->first();

        // Test 1-click Pay & Record Payment
        Livewire::test(\App\Livewire\Subscriptions\Index::class)
            ->call('recordPayment', $sub->id);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'type' => 'expense',
            'amount' => 240000,
        ]);

        $this->assertEquals(14760000, $this->account->fresh()->current_balance);
    }

    public function test_invoice_aging_and_whatsapp_followup_link(): void
    {
        $this->actingAs($this->user);

        $client = Client::create([
            'user_id' => $this->user->id,
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);

        $project = Project::create([
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'name' => 'Redesign Website Company',
            'total_price' => 5000000,
        ]);

        $invoice = Invoice::create([
            'project_id' => $project->id,
            'invoice_number' => 'INV-2026-001',
            'amount' => 5000000,
            'status' => 'sent',
            'issue_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(5),
        ]);

        $component = Livewire::test(\App\Livewire\Clients\Index::class);
        $component->assertOk();

        $waLink = $component->instance()->getWhatsAppLink($invoice);
        $this->assertNotNull($waLink);
        $this->assertStringContainsString('628123456789', $waLink);
        $this->assertStringContainsString('INV-2026-001', urldecode($waLink));
    }

    public function test_ai_copilot_service_and_livewire_component(): void
    {
        $this->actingAs($this->user);

        $advisor = app(AiFinancialAdvisorService::class);
        $snap = $advisor->getSnapshot($this->user->id);

        $this->assertGreaterThan(0, $snap['total_balance']);
        $this->assertGreaterThan(0, $snap['available_money']);

        // Test Q&A reasoning
        $ans1 = $advisor->ask($this->user->id, 'Bisa beli laptop 5 juta gak?');
        $this->assertArrayHasKey('verdict', $ans1);
        $this->assertArrayHasKey('message', $ans1);

        $ans2 = $advisor->ask($this->user->id, 'Berapa bulan runway saya?');
        $this->assertArrayHasKey('verdict', $ans2);

        // Test Livewire Copilot
        Livewire::test(\App\Livewire\AiCopilot\Index::class)
            ->assertSee('Coming Soon')
            ->assertSee('AI Financial Copilot');
    }

    public function test_financial_statement_pdf_report_and_csv_export(): void
    {
        $this->actingAs($this->user);

        // 1. Report Statement Page
        $repResponse = $this->get(route('reports.financial-statement'));
        $repResponse->assertOk();
        $repResponse->assertSee('Laporan Arus Kas');
        $repResponse->assertSee($this->user->name);

        // 2. CSV Export
        $csvResponse = $this->get(route('reports.export-csv'));
        $csvResponse->assertOk();
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
    }
}
