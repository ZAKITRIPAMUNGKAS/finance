<?php

namespace Tests\Feature;

use App\Livewire\Accounts\Index as AccountsIndex;
use App\Livewire\Analytics\Index as AnalyticsIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Budgets\Index as BudgetsIndex;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Planning\PurchasePlanning;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\QuickTransactionModal;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Livewire\Wishlists\Index as WishlistsIndex;
use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\PurchaseSaving;
use App\Models\PurchaseWishlist;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AvailableMoneyService;
use App\Services\PurchasePlanningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_observer_adjusts_account_balance_correctly()
    {
        $accountA = Account::create([
            'name' => 'BCA Utama',
            'type' => 'bank',
            'initial_balance' => 5000000,
            'current_balance' => 5000000,
        ]);

        $accountB = Account::create([
            'name' => 'GoPay',
            'type' => 'ewallet',
            'initial_balance' => 500000,
            'current_balance' => 500000,
        ]);

        // 1. Income transaction
        Transaction::create([
            'account_id' => $accountA->id,
            'type' => 'income',
            'amount' => 2000000,
            'date' => now(),
            'description' => 'DP Client Project',
        ]);
        $this->assertEquals(7000000, $accountA->fresh()->current_balance);

        // 2. Expense transaction
        Transaction::create([
            'account_id' => $accountA->id,
            'type' => 'expense',
            'amount' => 1000000,
            'date' => now(),
            'description' => 'Sewa Lensa',
        ]);
        $this->assertEquals(6000000, $accountA->fresh()->current_balance);

        // 3. Transfer transaction
        Transaction::create([
            'account_id' => $accountA->id,
            'destination_account_id' => $accountB->id,
            'type' => 'transfer',
            'amount' => 500000,
            'date' => now(),
            'description' => 'Top Up GoPay',
        ]);
        $this->assertEquals(5500000, $accountA->fresh()->current_balance);
        $this->assertEquals(1000000, $accountB->fresh()->current_balance);
    }

    public function test_purchase_saving_observer_syncs_wishlist_saved_amount_and_status()
    {
        $wishlist = PurchaseWishlist::create([
            'name' => 'DJI Pocket 4',
            'category' => 'Alat Kerja',
            'target_price' => 8000000,
            'current_price' => 8000000,
            'priority' => 'high',
            'saved_amount' => 0,
            'status' => 'planning',
        ]);

        // Add partial saving
        PurchaseSaving::create([
            'wishlist_id' => $wishlist->id,
            'amount' => 3000000,
            'date' => now(),
            'note' => 'Setoran 1',
        ]);

        $wishlist->refresh();
        $this->assertEquals(3000000, $wishlist->saved_amount);
        $this->assertEquals('saving', $wishlist->status);
        $this->assertEquals(5000000, $wishlist->shortage_amount);
        $this->assertEquals(37.5, $wishlist->progress_percentage);

        // Add remaining saving to reach 100%
        PurchaseSaving::create([
            'wishlist_id' => $wishlist->id,
            'amount' => 5000000,
            'date' => now(),
            'note' => 'Setoran 2 (Pelunasan target)',
        ]);

        $wishlist->refresh();
        $this->assertEquals(8000000, $wishlist->saved_amount);
        $this->assertEquals('ready', $wishlist->status);
        $this->assertEquals(0, $wishlist->shortage_amount);
        $this->assertEquals(100, $wishlist->progress_percentage);
    }

    public function test_available_money_and_purchase_planning_simulation()
    {
        Account::create([
            'name' => 'BCA',
            'type' => 'bank',
            'initial_balance' => 20000000,
            'current_balance' => 20000000,
        ]);

        $wishlist = PurchaseWishlist::create([
            'name' => 'Kamera Baru',
            'target_price' => 15000000,
            'current_price' => 15000000,
            'saved_amount' => 0,
            'status' => 'planning',
        ]);

        PurchaseSaving::create([
            'wishlist_id' => $wishlist->id,
            'amount' => 5000000,
            'date' => now(),
        ]);

        $availableMoneyService = app(AvailableMoneyService::class);
        $this->assertEquals(20000000, $availableMoneyService->getTotalBalance());
        $this->assertEquals(5000000, $availableMoneyService->getTotalWishlistAllocated());
        $this->assertEquals(15000000, $availableMoneyService->getAvailableMoney());

        // Test Purchase Planning Service simulation
        $planningService = app(PurchasePlanningService::class);
        $eval = $planningService->evaluatePurchase(10000000, 5000000);
        $this->assertArrayHasKey('recommendation', $eval);
        $this->assertArrayHasKey('metrics', $eval);
    }

    public function test_all_livewire_components_render_and_function_correctly()
    {
        $acc1 = Account::create(['name' => 'BCA', 'type' => 'bank', 'initial_balance' => 10000000, 'current_balance' => 10000000]);
        $acc2 = Account::create(['name' => 'GoPay', 'type' => 'ewallet', 'initial_balance' => 1000000, 'current_balance' => 1000000]);
        $cat = Category::create(['name' => 'Food', 'type' => 'expense', 'color' => '#EF4444']);
        $client = Client::create(['name' => 'PT Test', 'company' => 'PT Test Corp']);

        // 1. Dashboard renders
        Livewire::test(Dashboard::class)->assertStatus(200);

        // 2. QuickTransactionModal opens and saves transfer
        Livewire::test(QuickTransactionModal::class)
            ->call('openTransferModal')
            ->assertSet('isOpen', true)
            ->assertSet('type', 'transfer')
            ->set('account_id', $acc1->id)
            ->set('destination_account_id', $acc2->id)
            ->set('amount', '500000')
            ->set('description', 'Transfer ke GoPay')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertEquals(9500000, $acc1->fresh()->current_balance);
        $this->assertEquals(1500000, $acc2->fresh()->current_balance);

        // 3. Transactions Index renders & deletes
        $tx = Transaction::latest()->first();
        Livewire::test(TransactionsIndex::class)
            ->assertStatus(200)
            ->call('deleteTransaction', $tx->id);

        // 4. Wishlists Index CRUD & price tracking & saving allocation
        $wishlist = PurchaseWishlist::create([
            'name' => 'MacBook Pro M3',
            'category' => 'Alat Kerja',
            'target_price' => 30000000,
            'current_price' => 30000000,
            'priority' => 'critical',
            'status' => 'planning',
        ]);

        Livewire::test(WishlistsIndex::class)
            ->assertStatus(200)
            ->call('openPriceModal', $wishlist->id)
            ->assertSet('isPriceModalOpen', true)
            ->set('new_price', '28500000')
            ->call('recordPriceUpdate')
            ->call('openSavingModal', $wishlist->id)
            ->assertSet('isSavingModalOpen', true)
            ->set('saving_amount', '5000000')
            ->call('allocateSaving');

        $this->assertEquals(28500000, $wishlist->fresh()->current_price);
        $this->assertEquals(5000000, $wishlist->fresh()->saved_amount);

        // 5. Purchase Planning Simulator
        Livewire::test(PurchasePlanning::class, ['wishlist_id' => $wishlist->id])
            ->assertStatus(200)
            ->assertSet('purchasePrice', '28500000');

        // 6. Projects Index: Add cost and invoice
        $project = Project::create([
            'client_id' => $client->id,
            'name' => 'Livestreaming Event',
            'category' => 'livestream',
            'total_revenue' => 15000000,
            'status' => 'in_progress',
        ]);

        Livewire::test(ProjectsIndex::class)
            ->assertStatus(200)
            ->call('openAddCostModal', $project->id)
            ->assertSet('isCostModalOpen', true)
            ->set('cost_description', 'Sewa Kamera')
            ->set('cost_amount', '2000000')
            ->call('saveCost')
            ->call('openAddInvoiceModal', $project->id)
            ->assertSet('isInvoiceModalOpen', true)
            ->set('invoice_number', 'INV-TEST-001')
            ->set('invoice_amount', '15000000')
            ->call('saveInvoice');

        $this->assertEquals(2000000, $project->fresh()->total_cost);
        $this->assertEquals(13000000, $project->fresh()->profit);

        // 7. Clients Index: Mark invoice paid
        $invoice = Invoice::where('invoice_number', 'INV-TEST-001')->first();
        Livewire::test(ClientsIndex::class)
            ->assertStatus(200)
            ->call('openMarkPaidModal', $invoice->id)
            ->assertSet('isPaidModalOpen', true)
            ->set('payAccountId', $acc1->id)
            ->call('confirmMarkPaid');

        $this->assertEquals('paid', $invoice->fresh()->status);

        // 8. Budgets Index (PRD v1.2 Budget Engine)
        Livewire::test(BudgetsIndex::class)
            ->assertStatus(200)
            ->call('openConfigModal')
            ->assertSet('isConfigModalOpen', true)
            ->call('applyEmaSuggestions')
            ->call('saveConfiguration');

        // 9. Analytics Index (with Radar chart dataset)
        Livewire::test(AnalyticsIndex::class)
            ->assertStatus(200)
            ->assertSee('Pentagon Radar');
    }

    public function test_welcome_auth_and_settings_flow()
    {
        // 1. Welcome Landing Page returns 200
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Available Money');

        // 2. Register flow
        Livewire::test(Register::class)
            ->set('name', 'Budi Developer')
            ->set('email', 'budi@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->set('initial_balance', '7500000')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
        $budi = User::where('email', 'budi@example.com')->first();
        $this->assertDatabaseHas('accounts', ['user_id' => $budi->id, 'name' => 'BCA Utama']);

        // 3. Login flow with quick demo
        Livewire::test(Login::class)
            ->call('quickDemoLogin');

        // 4. Settings flow
        Livewire::actingAs($budi);
        Livewire::test(SettingsIndex::class)
            ->assertStatus(200)
            ->set('name', 'Budi Pratama')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertEquals('Budi Pratama', $budi->fresh()->name);
    }
}
