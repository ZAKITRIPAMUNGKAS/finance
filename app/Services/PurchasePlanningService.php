<?php

namespace App\Services;

use App\Models\PurchaseWishlist;

class PurchasePlanningService
{
    public function __construct(
        protected AvailableMoneyService $availableMoneyService
    ) {}

    /**
     * Simulasi kelayakan finansial pembelian ("Can I Afford This?")
     */
    public function evaluatePurchase(
        float $purchasePrice,
        float $dedicatedSavings = 0,
        ?PurchaseWishlist $wishlist = null,
        ?int $userId = null
    ): array {
        $totalBalance = $this->availableMoneyService->getTotalBalance($userId);
        $availableMoney = $this->availableMoneyService->getAvailableMoney($userId);
        $emergencyMonthsBefore = $this->availableMoneyService->getEmergencyFundMonths($userId);
        $avgMonthlyExpense = $this->availableMoneyService->getAverageMonthlyExpense($userId);

        // Jika ada dedicated saving (misal dari wishlist yang sudah terkumpul)
        $effectiveOutflowFromGeneral = max(0, $purchasePrice - $dedicatedSavings);

        // Dampak terhadap available money
        $availableMoneyAfter = $availableMoney - $effectiveOutflowFromGeneral;

        // Dampak terhadap total balance & dana darurat
        $totalBalanceAfter = $totalBalance - $purchasePrice;
        $emergencyMonthsAfter = $avgMonthlyExpense > 0 ? round(max(0, $totalBalanceAfter) / $avgMonthlyExpense, 1) : 0;

        // Penentuan Rekomendasi
        if ($dedicatedSavings >= $purchasePrice) {
            $recommendation = 'SAFE';
            $badgeColor = 'emerald';
            $title = '🟢 SAFE TO PURCHASE';
            $description = 'Dana khusus tabungan untuk barang ini sudah mencukupi 100%. Pembelian ini tidak mengganggu Available Money maupun Dana Darurat Anda.';
        } elseif ($availableMoneyAfter >= 0 && $emergencyMonthsAfter >= 3.0) {
            $recommendation = 'SAFE';
            $badgeColor = 'emerald';
            $title = '🟢 SAFE TO PURCHASE';
            $description = 'Available Money dan Dana Darurat tetap terjaga di level sehat (≥ 3 bulan pengeluaran) setelah pembelian ini.';
        } elseif ($availableMoneyAfter >= 0 && $emergencyMonthsAfter >= 1.5) {
            $recommendation = 'CAUTION';
            $badgeColor = 'amber';
            $title = '🟡 PROCEED WITH CAUTION';
            $description = 'Uang riil mencukupi, namun Dana Darurat akan tergerus menjadi ' . $emergencyMonthsAfter . ' bulan. Pastikan tidak ada pengeluaran darurat dalam waktu dekat.';
        } else {
            $recommendation = 'NOT_RECOMMENDED';
            $badgeColor = 'rose';
            $title = '🔴 NOT RECOMMENDED';
            $description = 'Pembelian ini akan menyebabkan defisit pada Available Money (uang terikat) atau memangkas Dana Darurat di bawah batas aman (< 1.5 bulan).';
        }

        return [
            'purchase_price' => $purchasePrice,
            'dedicated_savings' => $dedicatedSavings,
            'effective_outflow' => $effectiveOutflowFromGeneral,
            'recommendation' => $recommendation,
            'badge_color' => $badgeColor,
            'title' => $title,
            'description' => $description,
            'metrics' => [
                'current_balance_before' => $totalBalance,
                'current_balance_after' => $totalBalanceAfter,
                'available_money_before' => $availableMoney,
                'available_money_after' => $availableMoneyAfter,
                'emergency_months_before' => $emergencyMonthsBefore,
                'emergency_months_after' => $emergencyMonthsAfter,
            ],
            'wishlist' => $wishlist,
        ];
    }
}
