<?php

namespace App\Livewire\Planning;

use App\Models\PurchaseWishlist;
use App\Services\AvailableMoneyService;
use App\Services\PurchasePlanningService;
use Livewire\Component;

class PurchasePlanning extends Component
{
    public ?int $selectedWishlistId = null;
    public string $itemName = '';
    public string $purchasePrice = '';
    public string $dedicatedSavings = '0';
    public bool $useWishlistData = false;

    public array $simulationResult = [];

    public function mount(
        PurchasePlanningService $planner,
        ?int $wishlist_id = null
    ) {
        if ($wishlist_id) {
            $this->selectedWishlistId = $wishlist_id;
            $this->loadWishlistData();
        } else {
            $this->runSimulation($planner);
        }
    }

    public function updatedSelectedWishlistId()
    {
        $this->loadWishlistData();
    }

    public function loadWishlistData()
    {
        if ($this->selectedWishlistId) {
            $wishlist = PurchaseWishlist::where('user_id', auth()->id())->find($this->selectedWishlistId);
            if ($wishlist) {
                $this->itemName = $wishlist->name;
                $this->purchasePrice = (string) (float) $wishlist->current_price;
                $this->dedicatedSavings = (string) (float) $wishlist->saved_amount;
                $this->useWishlistData = true;
            }
        } else {
            $this->useWishlistData = false;
        }

        $this->runSimulation(app(PurchasePlanningService::class));
    }

    public function updatedPurchasePrice()
    {
        $this->runSimulation(app(PurchasePlanningService::class));
    }

    public function updatedDedicatedSavings()
    {
        $this->runSimulation(app(PurchasePlanningService::class));
    }

    public function runSimulation(PurchasePlanningService $planner)
    {
        $userId = auth()->id();
        $price = (float) $this->purchasePrice;
        $savings = (float) $this->dedicatedSavings;

        if ($price <= 0) {
            $price = 8000000; // default simulation price
            $this->purchasePrice = '8000000';
        }

        $wishlist = $this->selectedWishlistId ? PurchaseWishlist::where('user_id', $userId)->find($this->selectedWishlistId) : null;
        $this->simulationResult = $planner->evaluatePurchase($price, $savings, $wishlist, $userId);
    }

    public function render(AvailableMoneyService $availableMoneyService)
    {
        $userId = auth()->id();
        $wishlists = PurchaseWishlist::where('user_id', $userId)->whereIn('status', ['planning', 'saving', 'ready'])->get();
        $totalBalance = $availableMoneyService->getTotalBalance($userId);
        $availableMoney = $availableMoneyService->getAvailableMoney($userId);
        $emergencyMonths = $availableMoneyService->getEmergencyFundMonths($userId);

        return view('livewire.planning.purchase-planning', compact('wishlists', 'totalBalance', 'availableMoney', 'emergencyMonths'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Can I Afford This? Simulator',
                'headerSubtitle' => 'Evaluasi dampak instan pembelian barang terhadap Available Money & Dana Darurat'
            ]);
    }
}
