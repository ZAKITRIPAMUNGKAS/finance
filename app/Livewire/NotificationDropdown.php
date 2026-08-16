<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\PurchaseWishlist;
use App\Services\AvailableMoneyService;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $isOpen = false;
    public array $dismissedIds = [];

    protected $listeners = [
        'transaction-saved' => '$refresh',
        'wishlist-updated'  => '$refresh',
        'saving-recorded'   => '$refresh',
    ];

    public function mount(): void
    {
        $this->dismissedIds = session()->get('dismissed_notifications', []);
    }

    public function toggleDropdown(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeDropdown(): void
    {
        $this->isOpen = false;
    }

    public function markAllAsRead(): void
    {
        $notifications = $this->getNotifications();
        foreach ($notifications as $n) {
            $this->dismissedIds[] = $n['id'];
        }
        $this->dismissedIds = array_unique($this->dismissedIds);
        session()->put('dismissed_notifications', $this->dismissedIds);
    }

    public function dismissNotification(string $id): void
    {
        $this->dismissedIds[] = $id;
        $this->dismissedIds = array_unique($this->dismissedIds);
        session()->put('dismissed_notifications', $this->dismissedIds);
    }

    public function getNotifications(): array
    {
        $userId = auth()->id();
        if (!$userId) {
            return [];
        }

        $items = [];

        // 1. Overdue Invoices
        $overdueInvoices = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->with('project.client')
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->get();

        foreach ($overdueInvoices as $inv) {
            $id = 'inv_overdue_' . $inv->id;
            if (!in_array($id, $this->dismissedIds)) {
                $items[] = [
                    'id' => $id,
                    'type' => 'danger',
                    'icon' => 'alert-triangle',
                    'title' => 'Invoice Jatuh Tempo',
                    'message' => "Invoice {$inv->invoice_number} (Rp " . number_format($inv->amount, 0, ',', '.') . ") dari " . ($inv->project?->client?->name ?? 'Klien') . " telah melewati batas waktu.",
                    'time' => \Carbon\Carbon::parse($inv->due_date)->diffForHumans(),
                    'link' => route('clients'),
                ];
            }
        }

        // 2. Upcoming Invoices Due (Next 3 Days)
        $upcomingInvoices = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->with('project.client')
            ->where('status', '!=', 'paid')
            ->whereBetween('due_date', [now()->format('Y-m-d'), now()->addDays(3)->format('Y-m-d')])
            ->get();

        foreach ($upcomingInvoices as $inv) {
            $id = 'inv_due_' . $inv->id;
            if (!in_array($id, $this->dismissedIds)) {
                $items[] = [
                    'id' => $id,
                    'type' => 'warning',
                    'icon' => 'clock',
                    'title' => 'Jatuh Tempo Segera',
                    'message' => "Invoice {$inv->invoice_number} sebesar Rp " . number_format($inv->amount, 0, ',', '.') . " jatuh tempo " . \Carbon\Carbon::parse($inv->due_date)->diffForHumans() . ".",
                    'time' => 'Dalam 3 hari',
                    'link' => route('clients'),
                ];
            }
        }

        // 3. Wishlist Near / Completed Goal
        $fundedWishlists = PurchaseWishlist::where('user_id', $userId)
            ->where('saved_amount', '>', 0)
            ->get();

        foreach ($fundedWishlists as $w) {
            $pct = $w->current_price > 0 ? round(($w->saved_amount / $w->current_price) * 100) : 0;
            if ($pct >= 100) {
                $id = 'wishlist_ready_' . $w->id;
                if (!in_array($id, $this->dismissedIds)) {
                    $items[] = [
                        'id' => $id,
                        'type' => 'success',
                        'icon' => 'check-circle',
                        'title' => 'Wishlist Siap Dibeli! 🎉',
                        'message' => "Target tabungan untuk \"{$w->name}\" sudah 100% tercapai (Rp " . number_format($w->saved_amount, 0, ',', '.') . ").",
                        'time' => 'Siap Checkout',
                        'link' => route('wishlists'),
                    ];
                }
            }
        }

        // 4. Available Money Status Tip
        $availableMoney = app(AvailableMoneyService::class)->getAvailableMoney($userId);
        if ($availableMoney > 0) {
            $id = 'avail_money_tip_' . now()->format('Y-m-d');
            if (!in_array($id, $this->dismissedIds)) {
                $items[] = [
                    'id' => $id,
                    'type' => 'info',
                    'icon' => 'wallet',
                    'title' => 'Available Money Sehat',
                    'message' => 'Uang Bebas Anda saat ini Rp ' . number_format($availableMoney, 0, ',', '.') . ' aman dialokasikan untuk kebutuhan pribadi.',
                    'time' => 'Hari ini',
                    'link' => route('dashboard'),
                ];
            }
        }

        return $items;
    }

    public function render()
    {
        $notifications = $this->getNotifications();
        $unreadCount = count($notifications);

        return view('livewire.notification-dropdown', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
