<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class MerchantPricingCalculator extends Component
{
    public bool $isOpen = false;
    public string $productName = 'Kemeja Oversize Katun';
    public string $baseCost = '65000'; // HPP modal beli
    public string $packingCost = '3000'; // Biaya packing, bubble wrap, plastik
    public string $marketplaceFeePercent = '6.5'; // Biaya admin Shopee / Tokped
    public string $targetProfitPercent = '30'; // Target margin laba bersih (%)

    protected $listeners = [
        'open-merchant-calculator' => 'open',
    ];

    public function open()
    {
        $this->isOpen = true;
    }

    public function getCleanBaseCostProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', (string) $this->baseCost);
    }

    public function getCleanPackingCostProperty(): float
    {
        return (float) preg_replace('/[^\d]/', '', (string) $this->packingCost);
    }

    public function getMarketplaceFeeProperty(): float
    {
        return (float) $this->marketplaceFeePercent;
    }

    public function getTargetProfitProperty(): float
    {
        return (float) $this->targetProfitPercent;
    }

    /**
     * Menghitung Harga Jual Ideal
     * Rumus: (Modal HPP + Biaya Packing) / (1 - (Admin Fee% + Target Margin%)/100)
     */
    public function getRecommendedSellingPriceProperty(): float
    {
        $totalDirectCost = $this->cleanBaseCost + $this->cleanPackingCost;
        $feeAndMarginRate = ($this->marketplaceFee + $this->targetProfit) / 100;

        if ($feeAndMarginRate >= 0.95) {
            $feeAndMarginRate = 0.90; // prevent division by zero or negative
        }

        $sellingPrice = $totalDirectCost / (1 - $feeAndMarginRate);
        return round($sellingPrice, -2); // round to nearest hundreds (e.g. 98.500)
    }

    public function getAdminFeeDeductionProperty(): float
    {
        return round($this->recommendedSellingPrice * ($this->marketplaceFee / 100));
    }

    public function getNetProfitProperty(): float
    {
        return $this->recommendedSellingPrice - $this->cleanBaseCost - $this->cleanPackingCost - $this->adminFeeDeduction;
    }

    public function render()
    {
        return view('livewire.tools.merchant-pricing-calculator');
    }
}
