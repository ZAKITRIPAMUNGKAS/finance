<?php

namespace App\Services;

use App\Models\PurchaseWishlist;
use App\Models\Transaction;
use Carbon\Carbon;

class SavingPlanService
{
    /**
     * Rata-rata kapasitas saving riil per bulan (Income - Expense) selama 3 bulan terakhir
     */
    public function getHistoricalMonthlySavingCapacity(?int $userId = null, int $months = 3): float
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        $endDate = now()->subMonth()->endOfMonth();

        $incomeQuery = Transaction::where('type', 'income')->whereBetween('date', [$startDate, $endDate]);
        $expenseQuery = Transaction::where('type', 'expense')->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $incomeQuery->where('user_id', $userId);
            $expenseQuery->where('user_id', $userId);
        }

        $totalIncome = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');

        $avgNet = ($totalIncome - $totalExpense) / $months;

        // Jika belum ada data historis yang cukup, default estimasi 2.000.000
        return $avgNet > 0 ? $avgNet : 2000000;
    }

    /**
     * Evaluasi kelayakan saving plan untuk 1 item wishlist
     */
    public function evaluateItemPlan(PurchaseWishlist $wishlist, ?int $userId = null): array
    {
        $shortage = $wishlist->shortage_amount;
        $remainingMonths = $wishlist->remaining_months;
        $monthlyNeed = $wishlist->monthly_saving_need;
        $avgSavingCapacity = $this->getHistoricalMonthlySavingCapacity($userId);

        if ($shortage <= 0) {
            return [
                'status' => 'ready',
                'badge' => 'ready',
                'label' => 'Dana Terkumpul',
                'color' => 'emerald',
                'monthly_need' => 0,
                'remaining_months' => $remainingMonths,
                'note' => 'Target tabungan sudah 100% tercapai! Siap dibeli.',
            ];
        }

        if ($monthlyNeed <= $avgSavingCapacity) {
            return [
                'status' => 'realistic',
                'badge' => 'realistic',
                'label' => 'Realistis',
                'color' => 'emerald',
                'monthly_need' => $monthlyNeed,
                'remaining_months' => $remainingMonths,
                'note' => 'Kebutuhan Rp ' . number_format($monthlyNeed, 0, ',', '.') . '/bulan sesuai dengan kapasitas tabungan historis Anda (± Rp ' . number_format($avgSavingCapacity, 0, ',', '.') . '/bln).',
            ];
        } elseif ($monthlyNeed <= ($avgSavingCapacity * 1.35)) {
            // Berisiko mundur
            $estimatedMonths = ceil($shortage / max(1, $avgSavingCapacity));
            $delayMonths = max(1, $estimatedMonths - $remainingMonths);

            return [
                'status' => 'at_risk',
                'badge' => 'at_risk',
                'label' => 'Berisiko Mundur',
                'color' => 'amber',
                'monthly_need' => $monthlyNeed,
                'remaining_months' => $remainingMonths,
                'delay_months' => $delayMonths,
                'note' => 'Kebutuhan sedikit di atas rata-rata tabungan. Kemungkinan target bergeser mundur ± ' . $delayMonths . ' bulan.',
            ];
        } else {
            // Tidak realistis
            $realisticMonths = ceil($shortage / max(1, $avgSavingCapacity));

            return [
                'status' => 'unrealistic',
                'badge' => 'unrealistic',
                'label' => 'Tidak Realistis',
                'color' => 'rose',
                'monthly_need' => $monthlyNeed,
                'remaining_months' => $remainingMonths,
                'suggested_months' => $realisticMonths,
                'note' => 'Kebutuhan Rp ' . number_format($monthlyNeed, 0, ',', '.') . '/bulan jauh di atas kapasitas tabungan bulanan. Disarankan memundurkan target ke ± ' . $realisticMonths . ' bulan ke depan.',
            ];
        }
    }

    /**
     * Multi-wishlist summary & estimated timeline (PRD 6.4)
     */
    public function calculateMultiWishlistPlan(?int $userId = null): array
    {
        $query = PurchaseWishlist::whereIn('status', ['planning', 'saving']);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $activeWishlists = $query->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")->get();
        $totalTarget = (float) $activeWishlists->sum('current_price');
        $totalSaved = (float) $activeWishlists->sum('saved_amount');
        $totalShortage = max(0, $totalTarget - $totalSaved);

        $avgSavingCapacity = $this->getHistoricalMonthlySavingCapacity($userId);
        $estimatedTotalMonths = $avgSavingCapacity > 0 ? ceil($totalShortage / $avgSavingCapacity) : 0;

        return [
            'total_active_items' => $activeWishlists->count(),
            'total_target_value' => $totalTarget,
            'total_saved_value' => $totalSaved,
            'total_shortage' => $totalShortage,
            'avg_monthly_capacity' => $avgSavingCapacity,
            'estimated_completion_months' => $estimatedTotalMonths,
            'items' => $activeWishlists,
        ];
    }
}
