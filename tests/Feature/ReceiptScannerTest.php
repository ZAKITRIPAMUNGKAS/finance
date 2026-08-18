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

class ReceiptScannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'scanner@example.com',
            'name' => 'Scanner User',
        ]);

        $this->actingAs($this->user);

        // Seed accounts & categories
        $this->bca = Account::create([
            'user_id' => $this->user->id,
            'name' => 'BCA',
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
            'name' => 'Makan & Minum',
            'type' => 'expense',
            'is_business' => false,
        ]);

        $this->incomeCat = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Pemasukan Project Freelance',
            'type' => 'income',
            'is_business' => true,
        ]);
    }

    public function test_receipt_scanner_service_auto_selects_makan_dan_minum_for_coffee()
    {
        $rawText = "KOPI KENANGAN\nJl. Sudirman No 12 Jakarta\nTanggal: 16/08/2026\n1x Kenangan Mantan Large Rp 24.000\n1x Avocado Coffee Rp 24.000\nTOTAL BAYAR: Rp 48.000\nMetode Pembayaran: GoPay";

        $scanner = app(ReceiptScannerService::class);
        $result = $scanner->parseReceiptText($rawText);

        $this->assertEquals('expense', $result['type']);
        $this->assertEquals(48000, $result['amount']);
        $this->assertEquals('2026-08-16', $result['date']);
        $this->assertEquals('Kopi Kenangan', $result['description']);
        // Must automatically map Kopi / Kenangan to "Makan & Minum"
        $this->assertEquals($this->foodCat->id, $result['category_id']);
        $this->assertEquals($this->gopay->id, $result['account_id']);
    }

    public function test_receipt_scanner_service_parses_real_user_kopi_kenangan_receipt()
    {
        $rawText = "Hil KENANGAN\nORDER #B055\nJan 20 2024,15:45 PH\nTake Away 939000506792615759\n1 Kenangan Latte, Cold, Small, N 42,000\nTotal 42,000\nGopay QR 42,000\nPT Bumi Berkah Boga - 82.877.376.2-029.000\nWhatsApp +62-81-7073-9110";

        $scanner = app(ReceiptScannerService::class);
        $result = $scanner->parseReceiptText($rawText);

        // Must extract real total Rp 42.000, NOT the barcode 939000506792615759
        $this->assertEquals(42000, $result['amount']);
        $this->assertEquals('expense', $result['type']);
        $this->assertEquals('2024-01-20', $result['date']);
        $this->assertEquals('Kopi Kenangan', $result['description']);
        $this->assertEquals($this->foodCat->id, $result['category_id']);
        $this->assertEquals($this->gopay->id, $result['account_id']);
    }

    public function test_receipt_scanner_service_parses_client_transfer_income()
    {
        $rawText = "BCA Mobile\nTRANSFER MASUK BERHASIL\nTanggal: 15/08/2026\nDari: PT MAJU BERSAMA\nJumlah: Rp 7.500.000\nKeterangan: DP Project UI UX";

        $scanner = app(ReceiptScannerService::class);
        $result = $scanner->parseReceiptText($rawText);

        $this->assertEquals('income', $result['type']);
        $this->assertEquals(7500000, $result['amount']);
        $this->assertEquals('2026-08-15', $result['date']);
        $this->assertEquals($this->bca->id, $result['account_id']);
    }

    public function test_quick_transaction_modal_handles_scanned_ocr_and_saves()
    {
        $rawText = "Indomaret Point\nTanggal: 16/08/2026\nTOTAL: Rp 125.000\nMetode: BCA Debit";

        Livewire::test(QuickTransactionModal::class)
            ->call('processScannedText', $rawText)
            ->assertSet('type', 'expense')
            ->assertSet('amount', '125000')
            ->assertSet('date', '2026-08-16')
            ->assertSet('description', 'Belanja Indomaret')
            ->assertSet('category_id', $this->foodCat->id)
            ->assertSet('account_id', $this->bca->id)
            ->call('save')
            ->assertDispatched('transaction-saved');

        $this->assertDatabaseHas('transactions', [
            'amount' => 125000,
            'description' => 'Belanja Indomaret',
            'type' => 'expense',
            'account_id' => $this->bca->id,
            'category_id' => $this->foodCat->id,
        ]);
    }
}
