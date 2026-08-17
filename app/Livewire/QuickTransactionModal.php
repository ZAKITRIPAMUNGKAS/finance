<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Project;
use App\Models\Transaction;
use App\Services\AvailableMoneyService;
use App\Services\ReceiptScannerService;
use Livewire\Component;
use Livewire\WithFileUploads;

class QuickTransactionModal extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;

    public string $type = 'expense'; // expense, income, transfer
    public ?int $account_id = null;
    public ?int $destination_account_id = null;
    public ?int $category_id = null;
    public ?int $project_id = null;
    public string $amount = '';
    public string $date = '';
    public string $description = '';
    public ?string $notes = null;

    // OCR & Image Scanning Properties
    public $receiptImage = null;
    public bool $isScanning = false;
    public ?string $scanSuccessMessage = null;
    public ?string $previewImage = null;
    public string $rawScannedText = '';
    public bool $manualTextMode = false;
    public string $pastedText = '';

    // Voice Input Properties
    public bool $isListeningVoice = false;
    public string $voiceTranscript = '';

    // Budget impact
    public ?array $budgetImpact = null;

    // Wishlist saving pre-fill
    public ?int $savingWishlistId = null;

    protected $listeners = [
        'open-quick-add'      => 'openModal',
        'open-quick-voice'    => 'openVoiceModal',
        'open-quick-income'   => 'openIncomeModal',
        'open-quick-expense'  => 'openExpenseModal',
        'open-quick-transfer' => 'openTransferModal',
        'process-scanned-ocr' => 'processScannedText',
        'process-voice-text'  => 'processVoiceInput',
        'open-saving-modal'   => 'openSavingModal',
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $defaultAccount = Account::where('user_id', auth()->id())->where('is_active', true)->first();
        if ($defaultAccount) {
            $this->account_id = $defaultAccount->id;
        }
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function openVoiceModal()
    {
        $this->isOpen = true;
        $this->dispatch('start-voice-listening');
    }

    public function openIncomeModal()
    {
        $this->type = 'income';
        $this->isOpen = true;
    }

    public function openExpenseModal()
    {
        $this->type = 'expense';
        $this->isOpen = true;
    }

    public function openTransferModal()
    {
        $this->type = 'transfer';
        $this->isOpen = true;
    }

    /**
     * Pre-fill modal from Wishlist "Catat Saving" button.
     * Payload: ['wishlist_id', 'name', 'category_id']
     */
    public function openSavingModal(array $payload = []): void
    {
        $this->reset([
            'amount', 'notes', 'project_id', 'destination_account_id',
            'receiptImage', 'isScanning', 'scanSuccessMessage', 'previewImage', 'rawScannedText',
            'manualTextMode', 'pastedText', 'budgetImpact', 'isListeningVoice', 'voiceTranscript'
        ]);
        $this->date = now()->format('Y-m-d');

        $this->type              = 'expense';
        $this->savingWishlistId  = $payload['wishlist_id'] ?? null;
        $this->description       = 'Saving untuk ' . ($payload['name'] ?? 'Wishlist');
        $this->category_id       = $payload['category_id'] ?? null;
        $this->isOpen            = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset([
            'amount', 'description', 'notes', 'project_id', 'destination_account_id',
            'receiptImage', 'isScanning', 'scanSuccessMessage', 'previewImage', 'rawScannedText',
            'manualTextMode', 'pastedText', 'budgetImpact', 'savingWishlistId',
            'isListeningVoice', 'voiceTranscript'
        ]);
        $this->date = now()->format('Y-m-d');
    }

    public function setType(string $type)
    {
        $this->type = $type;
        $this->category_id = null;
        $this->budgetImpact = null;
    }

    public function toggleManualTextMode()
    {
        $this->manualTextMode = !$this->manualTextMode;
    }

    public function extractFromPastedText()
    {
        if (!empty(trim($this->pastedText))) {
            $this->processScannedText($this->pastedText);
            $this->pastedText = '';
            $this->manualTextMode = false;
        }
    }

    /**
     * Reactive: recalculate budget impact whenever amount or category changes.
     */
    public function updatedAmount(): void
    {
        $this->recalculateBudgetImpact();
    }

    public function updatedCategoryId(): void
    {
        $this->recalculateBudgetImpact();
    }

    private function recalculateBudgetImpact(): void
    {
        $this->budgetImpact = null;

        if ($this->type !== 'expense' || !$this->category_id || !$this->amount) {
            return;
        }

        $cleanAmount = (float) str_replace(['.', ','], ['', '.'], $this->amount);
        if ($cleanAmount <= 0) {
            $cleanAmount = (float) $this->amount;
        }
        if ($cleanAmount <= 0) {
            return;
        }

        // Check if there's an active budget for this category
        $now = now();
        $budget = Budget::where('category_id', $this->category_id)
            ->where(function ($q) use ($now) {
                $q->where(function ($sq) use ($now) {
                    $sq->where('period_year', $now->year)
                       ->where('period_month', $now->month);
                })->orWhere(function ($sq) {
                    $sq->whereNull('period_year')->whereNull('period_month');
                });
            })
            ->where('fixed_amount_limit', '>', 0)
            ->first();

        if (!$budget) {
            return;
        }

        $allocated = (float) $budget->fixed_amount_limit;

        // Sum spent so far this month in this category
        $spent = (float) Transaction::where('type', 'expense')
            ->where('category_id', $this->category_id)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $afterSpend  = $spent + $cleanAmount;
        $remaining   = max(0, $allocated - $afterSpend);
        $percentUsed = $allocated > 0 ? min(100, round(($afterSpend / $allocated) * 100)) : 0;

        $status = 'ok';
        if ($percentUsed >= 100) {
            $status = 'over';
        } elseif ($percentUsed >= 80) {
            $status = 'warning';
        }

        $this->budgetImpact = [
            'allocated'    => $allocated,
            'spent'        => $spent,
            'this_tx'      => $cleanAmount,
            'after_spend'  => $afterSpend,
            'remaining'    => $remaining,
            'percent_used' => $percentUsed,
            'status'       => $status,
        ];
    }

    /**
     * Handle Livewire file upload hook
     */
    public function updatedReceiptImage()
    {
        if ($this->receiptImage) {
            $filename = $this->receiptImage->getClientOriginalName();
            $this->scanSuccessMessage = "📷 Foto/struk terpilih ({$filename}). Membaca teks...";
        }
    }

    /**
     * Process voice transcription input.
     */
    public function processVoiceInput(string $spokenText)
    {
        $user = auth()->user();
        if ($user && !$user->canUseAiVoiceOrScan()) {
            $this->dispatch('open-upgrade-modal', feature: 'ai_voice');
            $this->scanSuccessMessage = '⚠️ Batas 5x AI Voice & Scan bulan ini telah tercapai. Upgrade ke PRO untuk akses tanpa batas!';
            return;
        }

        $scanner = app(ReceiptScannerService::class);
        $safeSpoken = $scanner->sanitizeUtf8($spokenText);
        $result = $scanner->parseVoiceText($safeSpoken, $this->type);

        $this->voiceTranscript = $safeSpoken;
        $this->type = $result['type'];
        if ($result['amount'] && $result['amount'] > 0) {
            $this->amount = (string) $result['amount'];
        }
        $this->date = $result['date'];

        if (!empty($result['description'])) {
            $this->description = $scanner->sanitizeUtf8($result['description']);
        }

        if ($result['category_id']) {
            $this->category_id = $result['category_id'];
        }
        if ($result['account_id']) {
            $this->account_id = $result['account_id'];
        }
        if ($result['project_id']) {
            $this->project_id = $result['project_id'];
        }

        $formattedAmount = $result['amount'] ? 'Rp ' . number_format($result['amount'], 0, ',', '.') : ($this->amount ? 'Rp ' . number_format((float)$this->amount, 0, ',', '.') : '');
        $this->scanSuccessMessage = $scanner->sanitizeUtf8("🎙️ Suara diproses: {$this->description}" . ($formattedAmount ? " ({$formattedAmount})" : ""));
        $this->isListeningVoice = false;

        if ($user) {
            $user->incrementAiUsage();
        }

        $this->recalculateBudgetImpact();
    }

    /**
     * Process scanned text from OCR or client canvas recognition.
     */
    public function processScannedText(string $rawText)
    {
        $user = auth()->user();
        if ($user && !$user->canUseAiVoiceOrScan()) {
            $this->dispatch('open-upgrade-modal', feature: 'ai_voice');
            $this->scanSuccessMessage = '⚠️ Batas 5x AI Voice & Scan bulan ini telah tercapai. Upgrade ke PRO untuk akses tanpa batas!';
            return;
        }

        $scanner = app(ReceiptScannerService::class);
        $safeRawText = $scanner->sanitizeUtf8($rawText);
        $result = $scanner->parseReceiptText($safeRawText, $this->type);
        $this->rawScannedText = $scanner->sanitizeUtf8(!empty($result['cleaned_text']) ? $result['cleaned_text'] : trim($safeRawText));

        if ($user) {
            $user->incrementAiUsage();
        }

        $this->type = $result['type'];
        if ($result['amount'] && $result['amount'] > 0) {
            $this->amount = (string) $result['amount'];
        }
        $this->date = $result['date'];

        if (!empty($result['description'])) {
            $this->description = $scanner->sanitizeUtf8($result['description']);
        }

        if ($result['category_id']) {
            $this->category_id = $result['category_id'];
        }
        if ($result['account_id']) {
            $this->account_id = $result['account_id'];
        }
        if ($result['project_id']) {
            $this->project_id = $result['project_id'];
        }

        $formattedAmount = $result['amount'] ? 'Rp ' . number_format($result['amount'], 0, ',', '.') : ($this->amount ? 'Rp ' . number_format((float)$this->amount, 0, ',', '.') : '');
        $this->scanSuccessMessage = $scanner->sanitizeUtf8("Berhasil dipindai: {$this->description}" . ($formattedAmount ? " ({$formattedAmount})" : ""));
        $this->isScanning = false;

        $this->recalculateBudgetImpact();
    }

    /**
     * Load sample simulated receipt text for 1-click test demo.
     */
    public function loadSampleReceipt(string $sampleType)
    {
        $samples = [
            'kopi'             => "KOPI KENANGAN\nJl. Sudirman No. 45 Jakarta\nTanggal: " . now()->format('d/m/Y') . "\n1x Kenangan Mantan Large Rp 24.000\n1x Avocado Coffee Rp 24.000\nTOTAL BAYAR: Rp 48.000\nPembayaran via GoPay Berhasil",
            'transfer_klien'   => "BCA Mobile\nTRANSFER MASUK BERHASIL\nTanggal: " . now()->format('d/m/Y') . "\nDari: PT MAJU BERSAMA KREASI\nJumlah: Rp 7.500.000\nKeterangan: DP Project Video Animasi 3D",
            'transfer_mandiri' => "Livin' by Mandiri\nTransfer Berhasil\nTanggal: " . now()->format('d/m/Y') . "\nPengirim: BUDI SANTOSO\nPenerima: ZAKI TRI PAMUNGKAS\nNominal: Rp 2.500.000\nCatatan: Pelunasan Jasa Desain UI/UX",
            'indomaret'        => "INDOMARET POINT\nTanggal: " . now()->format('d/m/Y') . "\n2x Roti Tawar Rp 30.000\n1x Susu UHT 1L Rp 22.000\n1x Minyak Goreng 2L Rp 36.000\nTOTAL: Rp 88.000\nMetode: Mandiri Debit",
            'adobe'            => "Adobe Systems Inc\nInvoice #AD-98234\nDate: " . now()->format('Y-m-d') . "\nCreative Cloud All Apps Subscription\nAmount: Rp 649.000\nPaid with Visa Card",
        ];

        if (isset($samples[$sampleType])) {
            $this->processScannedText($samples[$sampleType]);
        }
    }

    /**
     * Load sample simulated voice phrase for 1-click test demo.
     */
    public function loadSampleVoice(string $sampleType)
    {
        $samples = [
            'kopi'      => "Beli kopi kenangan 42 ribu pake gopay",
            'project'   => "Pemasukan DP project video 5 juta ke rekening BCA",
            'pelunasan' => "Pelunasan website 2,5 juta dari Klien Budi masuk Mandiri",
            'gaji'      => "Gaji freelance 7 juta 500 ribu masuk BCA",
            'bensin'    => "Beli bensin pertalite 50 ribu tunai",
            'wifi'      => "Bayar wifi indihome 350 ribu lewat Mandiri",
            'warteg'    => "Makan siang di warteg 25 ribu tunai",
        ];

        if (isset($samples[$sampleType])) {
            $this->processVoiceInput($samples[$sampleType]);
        }
    }

    public function save()
    {
        $userId = auth()->id();
        $rules = [
            'type'        => 'required|in:income,expense,transfer',
            'account_id'  => 'required|exists:accounts,id',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'description' => 'required|string|max:255',
        ];

        if ($this->type === 'transfer') {
            $rules['destination_account_id'] = 'required|exists:accounts,id|different:account_id';
        } else {
            $rules['category_id'] = 'nullable|exists:categories,id';
            $rules['project_id']  = 'nullable|exists:projects,id';
        }

        $this->validate($rules);

        $cleanAmount = (float) str_replace(['.', ','], ['', '.'], $this->amount);
        if ($cleanAmount <= 0) {
            $cleanAmount = (float) $this->amount;
        }

        $receiptPath = null;
        if ($this->receiptImage) {
            $receiptPath = $this->receiptImage->store('receipts', 'public');
        }

        $tx = Transaction::create([
            'user_id'                => $userId,
            'account_id'             => $this->account_id,
            'destination_account_id' => $this->type === 'transfer' ? $this->destination_account_id : null,
            'category_id'            => $this->type !== 'transfer' ? $this->category_id : null,
            'project_id'             => $this->type !== 'transfer' ? $this->project_id : null,
            'type'                   => $this->type,
            'amount'                 => $cleanAmount,
            'date'                   => $this->date,
            'description'            => $this->description,
            'receipt_image'          => $receiptPath,
            'notes'                  => $this->notes,
        ]);



        // Dedicated Wishlist Saving Bridging
        if ($this->savingWishlistId) {
            $wishlist = \App\Models\PurchaseWishlist::where('user_id', $userId)->find($this->savingWishlistId);
            if ($wishlist) {
                \App\Models\PurchaseSaving::create([
                    'wishlist_id'    => $wishlist->id,
                    'transaction_id' => $tx->id,
                    'account_id'     => $this->account_id,
                    'amount'         => $cleanAmount,
                    'date'           => $this->date,
                    'note'           => 'Saving via Quick Add: ' . $this->description,
                ]);

                if ($wishlist->saved_amount >= $wishlist->current_price) {
                    $wishlist->update(['status' => 'ready']);
                }
            }
        }

        // Calculate new available money for the toast payload
        $availableMoney = app(AvailableMoneyService::class)->getAvailableMoney($userId);

        $payload = [
            'amount'          => $cleanAmount,
            'type'            => $this->type,
            'description'     => $this->description,
            'available_money' => $availableMoney,
        ];

        $this->closeModal();

        // Dispatch rich event
        $this->dispatch('transaction-saved', ...$payload);
        $this->dispatch('refresh-data');
    }

    public function render()
    {
        $userId = auth()->id();
        $accounts   = Account::where('user_id', $userId)->where('is_active', true)->get();
        $categories = Category::where('user_id', $userId)->where('type', $this->type === 'income' ? 'income' : 'expense')->get();
        $projects   = Project::where('user_id', $userId)->whereIn('status', ['prospect', 'in_progress', 'completed'])->latest()->get();

        return view('livewire.quick-transaction-modal', compact('accounts', 'categories', 'projects'));
    }
}
