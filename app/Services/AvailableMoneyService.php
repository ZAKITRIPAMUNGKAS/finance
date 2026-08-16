<?php

namespace App\Services;

use App\Models\Account;
use App\Models\PurchaseWishlist;
use App\Models\Transaction;
use Carbon\Carbon;

class AvailableMoneyService
{
    /**
     * Total saldo seluruh akun aktif
     */
    public function getTotalBalance(?int $userId = null): float
    {
        $query = Account::where('is_active', true);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return (float) $query->sum('current_balance');
    }

    /**
     * Total dana yang sudah terikat/dialokasikan di Wishlist aktif (saving progress)
     */
    public function getTotalWishlistAllocated(?int $userId = null): float
    {
        $query = PurchaseWishlist::whereIn('status', ['planning', 'saving', 'ready']);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return (float) $query->sum('saved_amount');
    }

    /**
     * Dana operasional minimal / komitmen bulan berjalan
     */
    public function getCommittedOperationalBudget(?int $userId = null): float
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $query = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth]);
            
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Estimasi pengeluaran bulanan rutin rata-rata
        return (float) $query->sum('amount');
    }

    /**
     * Available Money riil yang bebas & aman dibelanjakan
     */
    public function getAvailableMoney(?int $userId = null): float
    {
        $totalBalance = $this->getTotalBalance($userId);
        $wishlistAllocated = $this->getTotalWishlistAllocated($userId);
        
        return $totalBalance - $wishlistAllocated;
    }

    /**
     * Rata-rata pengeluaran bulanan 3 bulan terakhir
     */
    public function getAverageMonthlyExpense(?int $userId = null, int $months = 3): float
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        $endDate = now()->subMonth()->endOfMonth();

        $query = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $total = (float) $query->sum('amount');
        return $total > 0 ? $total / $months : 2500000; // fallback standard expense
    }

    /**
     * Emergency Fund Ratio dalam satuan bulan
     */
    public function getEmergencyFundMonths(?int $userId = null): float
    {
        $avgExpense = $this->getAverageMonthlyExpense($userId);
        if ($avgExpense <= 0) {
            return 0;
        }

        $totalBalance = $this->getTotalBalance($userId);
        return round($totalBalance / $avgExpense, 1);
    }
}
