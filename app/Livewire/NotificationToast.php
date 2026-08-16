<?php

namespace App\Livewire;

use App\Services\AvailableMoneyService;
use Livewire\Component;

class NotificationToast extends Component
{
    public array $toasts = [];

    protected $listeners = [
        'transaction-saved'  => 'onTransactionSaved',
        'wishlist-updated'   => 'onWishlistUpdated',
        'saving-recorded'    => 'onSavingRecorded',
    ];

    /**
     * Called when a transaction is saved from QuickTransactionModal.
     * Receives payload: ['amount', 'type', 'description', 'available_money']
     */
    public function onTransactionSaved(array $payload = []): void
    {
        $amount      = $payload['amount'] ?? 0;
        $type        = $payload['type'] ?? 'expense';
        $description = $payload['description'] ?? 'Transaksi';
        $available   = $payload['available_money'] ?? null;

        if ($available === null) {
            $available = app(AvailableMoneyService::class)->getAvailableMoney();
        }

        $amountFmt    = 'Rp ' . number_format((float) $amount, 0, ',', '.');
        $availableFmt = 'Rp ' . number_format((float) $available, 0, ',', '.');

        if ($type === 'income') {
            $icon    = 'arrow-down-left';
            $color   = 'emerald';
            $message = "Pemasukan {$amountFmt} dicatat — {$description}";
            $sub     = "💰 Uang Bebas kini {$availableFmt}";
        } elseif ($type === 'transfer') {
            $icon    = 'arrow-right-left';
            $color   = 'indigo';
            $message = "Transfer {$amountFmt} berhasil — {$description}";
            $sub     = "🔄 Saldo teralokasi ulang";
        } else {
            $icon    = 'arrow-up-right';
            $color   = 'slate';
            $message = "Pengeluaran {$amountFmt} dicatat — {$description}";
            $sub     = "💳 Uang Bebas tersisa {$availableFmt}";
        }

        $this->push($icon, $color, $message, $sub);
    }

    public function onWishlistUpdated(array $payload = []): void
    {
        $name    = $payload['name'] ?? 'Wishlist';
        $action  = $payload['action'] ?? 'diperbarui';
        $this->push('shopping-bag', 'lime', "Wishlist \"{$name}\" {$action}", '🎯 Progress saving diperbarui');
    }

    public function onSavingRecorded(array $payload = []): void
    {
        $name      = $payload['wishlist'] ?? 'Wishlist';
        $amount    = $payload['amount'] ?? 0;
        $amountFmt = 'Rp ' . number_format((float) $amount, 0, ',', '.');
        $progress  = $payload['progress'] ?? null;
        $sub       = $progress !== null ? "🎯 Progress: {$progress}% tercapai" : '🎯 Dana saving bertambah';
        $this->push('piggy-bank', 'lime', "Saving {$amountFmt} untuk {$name}", $sub);
    }

    private function push(string $icon, string $color, string $message, string $sub): void
    {
        $id = uniqid('toast_', true);
        $this->toasts[] = compact('id', 'icon', 'color', 'message', 'sub');

        // Auto-remove after 5 s via JS dismiss; also limit queue
        if (count($this->toasts) > 4) {
            array_shift($this->toasts);
        }
    }

    public function dismiss(string $id): void
    {
        $this->toasts = array_values(array_filter($this->toasts, fn($t) => $t['id'] !== $id));
    }

    public function render()
    {
        return view('livewire.notification-toast');
    }
}
