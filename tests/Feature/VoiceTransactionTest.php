<?php

namespace Tests\Feature;

use App\Livewire\QuickTransactionModal;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReceiptScannerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoiceTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'voiceuser@example.com',
            'name' => 'Voice Tester',
        ]);

        $this->actingAs($this->user);

        $this->bca = Account::create([
            'user_id' => $this->user->id,
            'name' => 'BCA Rekening',
            'type' => 'bank',
            'account_number' => '1234567890',
            'current_balance' => 10000000,
            'is_active' => true,
        ]);

        $this->gopay = Account::create([
            'user_id' => $this->user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'current_balance' => 500000,
            'is_active' => true,
        ]);

        $this->foodCat = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Makanan & Minuman',
            'type' => 'expense',
            'is_business' => false,
        ]);

        $this->incomeCat = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Pendapatan Project Freelance',
            'type' => 'income',
            'is_business' => true,
        ]);
    }

    public function test_voice_service_parses_expense_kopi_kenangan(): void
    {
        $scanner = app(ReceiptScannerService::class);
        $result = $scanner->parseVoiceText('Beli kopi kenangan 42 ribu pake gopay');

        $this->assertEquals('expense', $result['type']);
        $this->assertEquals(42000, $result['amount']);
        $this->assertEquals('Kopi Kenangan', $result['description']);
        $this->assertEquals($this->foodCat->id, $result['category_id']);
        $this->assertEquals($this->gopay->id, $result['account_id']);
    }

    public function test_voice_service_parses_income_project_dp(): void
    {
        $scanner = app(ReceiptScannerService::class);
        $result = $scanner->parseVoiceText('Pemasukan DP project video 5 juta ke rekening BCA');

        $this->assertEquals('income', $result['type']);
        $this->assertEquals(5000000, $result['amount']);
        $this->assertStringContainsString('DP Project', $result['description']);
        $this->assertEquals($this->incomeCat->id, $result['category_id']);
        $this->assertEquals($this->bca->id, $result['account_id']);
    }

    public function test_voice_service_converts_spoken_number_words(): void
    {
        $scanner = app(ReceiptScannerService::class);

        $this->assertEquals(42000, $scanner->parseSpokenAmount('42 ribu'));
        $this->assertEquals(5000000, $scanner->parseSpokenAmount('5 juta'));
        $this->assertEquals(1500000, $scanner->parseSpokenAmount('1,5 juta'));
        $this->assertEquals(2500000, $scanner->parseSpokenAmount('2 juta 500 ribu'));
        $this->assertEquals(35000, $scanner->parseSpokenAmount('tiga puluh lima ribu'));
        $this->assertEquals(150000, $scanner->parseSpokenAmount('seratus lima puluh ribu'));
    }

    public function test_quick_transaction_modal_processes_voice_input_and_saves(): void
    {
        Livewire::test(QuickTransactionModal::class)
            ->call('openModal')
            ->call('processVoiceInput', 'Beli kopi kenangan 42 ribu pake gopay')
            ->assertSet('type', 'expense')
            ->assertSet('amount', '42000')
            ->assertSet('description', 'Kopi Kenangan')
            ->assertSet('category_id', $this->foodCat->id)
            ->assertSet('account_id', $this->gopay->id)
            ->call('save')
            ->assertDispatched('transaction-saved');

        $this->assertDatabaseHas('transactions', [
            'type' => 'expense',
            'amount' => 42000,
            'description' => 'Kopi Kenangan',
            'category_id' => $this->foodCat->id,
            'account_id' => $this->gopay->id,
        ]);
    }
}
