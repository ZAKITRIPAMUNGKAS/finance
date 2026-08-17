<?php

namespace App\Livewire\Wishlists;

use App\Models\Account;
use App\Models\Category;
use App\Models\PurchaseSaving;
use App\Models\PurchaseWishlist;
use App\Models\WishlistPriceHistory;
use App\Services\SavingPlanService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filters
    public string $filterPriority = 'all';
    public string $filterStatus = 'active'; // active (planning, saving, ready), purchased, cancelled, all
    public string $search = '';

    // Modal state for Add/Edit Wishlist
    public bool $isFormModalOpen = false;
    public ?int $wishlistId = null;
    public string $name = '';
    public string $category = 'Alat Kerja';
    public string $target_price = '';
    public string $current_price = '';
    public ?string $product_url = null;
    public string $priority = 'medium';
    public ?string $target_date = null;
    public string $status = 'planning';
    public ?string $notes = null;

    // Modal state for Manual Price Tracking
    public bool $isPriceModalOpen = false;
    public ?int $priceTrackingWishlistId = null;
    public string $new_price = '';
    public ?string $price_note = null;
    public $priceHistories = [];

    // Modal state for Saving Allocation
    public bool $isSavingModalOpen = false;
    public ?int $savingWishlistId = null;
    public ?int $savingAccountId = null;
    public string $saving_amount = '';
    public string $saving_date = '';
    public ?string $saving_note = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'target_price' => 'required|numeric|min:1',
        'current_price' => 'required|numeric|min:1',
        'product_url' => 'nullable|url',
        'priority' => 'required|in:critical,high,medium,low',
        'target_date' => 'nullable|date',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->saving_date = now()->format('Y-m-d');
        $defaultAccount = Account::where('user_id', auth()->id())->where('is_active', true)->first();
        if ($defaultAccount) {
            $this->savingAccountId = $defaultAccount->id;
        }
    }

    public function openCreateModal()
    {
        $this->reset(['wishlistId', 'name', 'notes', 'product_url']);
        $this->category = 'Alat Kerja';
        $this->target_price = '';
        $this->current_price = '';
        $this->priority = 'medium';
        $this->status = 'planning';
        $this->target_date = now()->addMonths(3)->format('Y-m-d');
        $this->isFormModalOpen = true;
    }

    public function openEditModal(int $id)
    {
        $wishlist = PurchaseWishlist::where('user_id', auth()->id())->findOrFail($id);
        $this->wishlistId = $wishlist->id;
        $this->name = $wishlist->name;
        $this->category = $wishlist->category;
        $this->target_price = $wishlist->target_price > 0 ? number_format($wishlist->target_price, 0, ',', '.') : '';
        $this->current_price = $wishlist->current_price > 0 ? number_format($wishlist->current_price, 0, ',', '.') : '';
        $this->product_url = $wishlist->product_url;
        $this->priority = $wishlist->priority;
        $this->target_date = $wishlist->target_date ? Carbon::parse($wishlist->target_date)->format('Y-m-d') : null;
        $this->status = $wishlist->status;
        $this->notes = $wishlist->notes;

        $this->isFormModalOpen = true;
    }

    public function saveWishlist()
    {
        $userId = auth()->id();
        $this->target_price = (string) str_replace(['.', ',', ' '], '', $this->target_price);
        $this->current_price = (string) str_replace(['.', ',', ' '], '', $this->current_price);

        if (empty($this->current_price) && !empty($this->target_price)) {
            $this->current_price = $this->target_price;
        }

        $this->validate();

        if ($this->wishlistId) {
            $wishlist = PurchaseWishlist::where('user_id', $userId)->findOrFail($this->wishlistId);
            $oldPrice = (float) $wishlist->current_price;
            $newPrice = (float) $this->current_price;

            $wishlist->update([
                'name' => $this->name,
                'category' => $this->category,
                'target_price' => $this->target_price,
                'current_price' => $this->current_price,
                'product_url' => $this->product_url,
                'priority' => $this->priority,
                'target_date' => $this->target_date,
                'notes' => $this->notes,
            ]);

            if ($oldPrice != $newPrice) {
                WishlistPriceHistory::create([
                    'wishlist_id' => $wishlist->id,
                    'price' => $newPrice,
                    'recorded_at' => now()->format('Y-m-d'),
                    'notes' => 'Update harga dari form edit',
                ]);
            }
        } else {
            $wishlist = PurchaseWishlist::create([
                'user_id' => $userId,
                'name' => $this->name,
                'category' => $this->category,
                'target_price' => $this->target_price,
                'current_price' => $this->current_price,
                'product_url' => $this->product_url,
                'priority' => $this->priority,
                'target_date' => $this->target_date,
                'saved_amount' => 0,
                'status' => 'planning',
                'notes' => $this->notes,
            ]);

            WishlistPriceHistory::create([
                'wishlist_id' => $wishlist->id,
                'price' => $this->current_price,
                'recorded_at' => now()->format('Y-m-d'),
                'notes' => 'Harga awal saat wishlist dibuat',
            ]);
        }

        $this->isFormModalOpen = false;
        $this->dispatch('wishlist-updated', name: $this->name, action: $this->wishlistId ? 'diperbarui' : 'ditambahkan');
        $this->dispatch('refresh-data');
    }

    public function openPriceModal(int $id)
    {
        $wishlist = PurchaseWishlist::where('user_id', auth()->id())->with('priceHistories')->findOrFail($id);
        $this->priceTrackingWishlistId = $wishlist->id;
        $this->new_price = $wishlist->current_price > 0 ? number_format($wishlist->current_price, 0, ',', '.') : '';
        $this->price_note = null;
        $this->priceHistories = $wishlist->priceHistories;
        $this->isPriceModalOpen = true;
    }

    public function recordPriceUpdate()
    {
        $this->new_price = (string) str_replace(['.', ',', ' '], '', $this->new_price);

        $this->validate([
            'new_price' => 'required|numeric|min:1',
            'price_note' => 'nullable|string|max:255',
        ]);

        $wishlist = PurchaseWishlist::where('user_id', auth()->id())->findOrFail($this->priceTrackingWishlistId);
        $wishlist->current_price = $this->new_price;
        $wishlist->save();

        WishlistPriceHistory::create([
            'wishlist_id' => $wishlist->id,
            'price' => $this->new_price,
            'recorded_at' => now()->format('Y-m-d'),
            'notes' => $this->price_note ?: 'Manual price check',
        ]);

        $this->isPriceModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function openSavingModal(int $id)
    {
        $wishlist = PurchaseWishlist::where('user_id', auth()->id())->findOrFail($id);
        $this->savingWishlistId = $wishlist->id;
        $this->saving_amount = '';
        $this->saving_note = null;
        $this->saving_date = now()->format('Y-m-d');
        $this->isSavingModalOpen = true;
    }

    public function openQuickSavingModal(int $id): void
    {
        $wishlist = PurchaseWishlist::where('user_id', auth()->id())->findOrFail($id);

        $savingCategory = Category::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->where('name', 'like', '%tabung%')
                  ->orWhere('name', 'like', '%saving%')
                  ->orWhere('name', 'like', '%nabung%');
            })->first();

        $this->dispatch('open-saving-modal', 
            wishlist_id: $wishlist->id,
            name: $wishlist->name,
            category_id: $savingCategory?->id,
        );
    }

    public function allocateSaving()
    {
        $userId = auth()->id();
        $this->saving_amount = (string) str_replace(['.', ',', ' '], '', $this->saving_amount);

        $this->validate([
            'saving_amount' => 'required|numeric|min:1',
            'saving_date' => 'required|date',
            'savingAccountId' => 'nullable|exists:accounts,id',
            'saving_note' => 'nullable|string|max:255',
        ]);

        $wishlist = PurchaseWishlist::where('user_id', $userId)->findOrFail($this->savingWishlistId);

        PurchaseSaving::create([
            'wishlist_id' => $wishlist->id,
            'account_id' => $this->savingAccountId,
            'amount' => $this->saving_amount,
            'date' => $this->saving_date,
            'note' => $this->saving_note ?: 'Alokasi tabungan',
        ]);

        $progress = $wishlist->current_price > 0
            ? min(100, round(($wishlist->fresh()->saved_amount / $wishlist->current_price) * 100))
            : 0;

        $this->isSavingModalOpen = false;
        $this->dispatch('saving-recorded',
            wishlist: $wishlist->name,
            amount: $this->saving_amount,
            progress: $progress,
        );
        $this->dispatch('refresh-data');
    }

    public function markAsPurchased(int $id)
    {
        $wishlist = PurchaseWishlist::where('user_id', auth()->id())->findOrFail($id);
        $wishlist->update([
            'status' => 'purchased',
            'purchased_at' => now(),
        ]);
        $this->dispatch('refresh-data');
    }

    public function deleteWishlist(int $id)
    {
        PurchaseWishlist::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->dispatch('refresh-data');
    }

    public function render(SavingPlanService $savingPlanService)
    {
        $userId = auth()->id();
        $query = PurchaseWishlist::where('user_id', $userId)->with(['savings', 'priceHistories']);

        if ($this->filterPriority !== 'all') {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterStatus === 'active') {
            $query->whereIn('status', ['planning', 'saving', 'ready']);
        } elseif ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $wishlists = $query->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->orderBy('target_date', 'asc')
            ->paginate(12);

        $wishlists->getCollection()->transform(function ($item) use ($savingPlanService, $userId) {
            $item->plan_eval = $savingPlanService->evaluateItemPlan($item, $userId);
            return $item;
        });

        $multiPlan = $savingPlanService->calculateMultiWishlistPlan($userId);
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();

        return view('livewire.wishlists.index', compact('wishlists', 'multiPlan', 'accounts'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Purchase Wishlist & Saving Plan',
                'headerSubtitle' => 'Modul v1.1: Perencanaan pembelian alat kerja & tracking tabungan terarah'
            ]);
    }
}
