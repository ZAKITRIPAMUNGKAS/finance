<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\PurchaseSaving;
use App\Models\Transaction;

class FinancialHealthService
{
    public function __construct(
        protected AvailableMoneyService $availableMoneyService
    ) {}

    /**
     * Hitung Financial Health Index (FHI) berbasis Weighted Composite Index (WCI),
     * Rolling 3-Month Window, Min-Max Normalization with Capping, dan Inverse Risk Modeling.
     *
     * Formula:
     * FHI = (0.25 * S_cashflow) + (0.20 * S_budget) + (0.20 * S_saving) + (0.20 * S_emergency) + (0.15 * S_receivables)
     *
     * @param int|null $userId
     * @return array
     */
    public function calculateScore(?int $userId = null): array
    {
        $now = now();
        $threeMonthsAgo = $now->copy()->subMonths(3)->startOfMonth();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();

        // -------------------------------------------------------------
        // ROLLING 3-MONTH BASELINE DATA (Mencegah distorsi volatilitas freelance)
        // -------------------------------------------------------------
        $total3mIncome = (float) Transaction::where('type', 'income')
            ->where('date', '>=', $threeMonthsAgo)
            ->where('date', '<=', $currentMonthEnd)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->sum('amount');

        $total3mExpense = (float) Transaction::where('type', 'expense')
            ->where('date', '>=', $threeMonthsAgo)
            ->where('date', '<=', $currentMonthEnd)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->sum('amount');

        // Normalize per month (minimum divisor 1 to avoid zero division)
        $avg3mIncome = max(1000000, $total3mIncome / 3);
        $avg3mExpense = max(500000, $total3mExpense / 3);
        $avg3mSurplus = $avg3mIncome - $avg3mExpense;

        // -------------------------------------------------------------
        // PILAR 1: Cashflow Adequacy & Surplus Ratio (Bobot: 25% / 0.25)
        // Teori: Surplus Ratio = (Avg Income - Avg Expense) / Avg Income
        // Min-Max Scale: <= 0% = 0 pts | 40%+ = 100 pts
        // -------------------------------------------------------------
        $surplusRatio = ($avg3mSurplus / $avg3mIncome) * 100;
        $s1_cashflow = $this->clamp(($surplusRatio / 40) * 100, 0, 100);

        // -------------------------------------------------------------
        // PILAR 2: Spending Control & Budget Adherence (Bobot: 20% / 0.20)
        // Teori: Menghitung deviasi / overbudget penalty terhadap limit dinamis
        // Skala: 100 - (Total Overbudget / Total Budgeted Limit * 100)
        // -------------------------------------------------------------
        $budgets = Budget::when($userId, fn($q) => $q->where('user_id', $userId))->get();
        $totalOverbudgetAmount = 0;
        $totalBudgetLimit = 0;
        $overbudgetCategoriesCount = 0;

        foreach ($budgets as $b) {
            $limit = $b->percentage > 0 ? ($avg3mIncome * ($b->percentage / 100)) : ($b->fixed_amount_limit ?? 0);
            $spent = (float) Transaction::where('type', 'expense')
                ->where('category_id', $b->category_id)
                ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
                ->sum('amount');

            $totalBudgetLimit += $limit;
            if ($limit > 0 && $spent > $limit) {
                $totalOverbudgetAmount += ($spent - $limit);
                $overbudgetCategoriesCount++;
            }
        }

        if ($totalBudgetLimit > 0) {
            $overrunRatio = ($totalOverbudgetAmount / $totalBudgetLimit) * 100;
            $s2_budget = $this->clamp(100 - ($overrunRatio * 2), 0, 100);
        } else {
            $s2_budget = $overbudgetCategoriesCount === 0 ? 90 : 60;
        }

        // -------------------------------------------------------------
        // PILAR 3: Savings Rate & Capital Formation (Bobot: 20% / 0.20)
        // Teori: (Tabungan Terkumpul + Surplus Rata-rata) / Income
        // Benchmark CFPB / Academic: 0% = 0 pts | 30%+ = 100 pts
        // -------------------------------------------------------------
        $recentSavingsAllocated = (float) PurchaseSaving::where('date', '>=', $threeMonthsAgo)
            ->sum('amount') / 3;

        $totalSavingsCapacity = max(0, $avg3mSurplus);
        $savingsRate = ($totalSavingsCapacity / $avg3mIncome) * 100;
        $s3_savings = $this->clamp(($savingsRate / 30) * 100, 0, 100);

        // -------------------------------------------------------------
        // PILAR 4: Emergency Fund Coverage (Bobot: 20% / 0.20)
        // Teori: Liquid Reserve Ratio = Available Money / Avg Monthly Expense
        // Benchmark CFPB: 0 bulan = 0 pts | 6 bulan cadangan = 100 pts (Capped)
        // -------------------------------------------------------------
        $availableMoney = $this->availableMoneyService->getAvailableMoney($userId);
        $emergencyMonths = $avg3mExpense > 0 ? ($availableMoney / $avg3mExpense) : 0;
        $s4_emergency = $this->clamp(($emergencyMonths / 6.0) * 100, 0, 100);

        // -------------------------------------------------------------
        // PILAR 5: Receivable Risk & Vulnerability (Bobot: 15% / 0.15)
        // Teori: Inverse Risk Factor dari piutang jatuh tempo (Overdue Invoices)
        // Skala: 0 Overdue = 100 pts | >30% Overdue = 0 pts
        // -------------------------------------------------------------
        $totalReceivables = (float) Invoice::whereIn('status', ['sent', 'overdue'])->sum('amount');
        $overdueReceivables = (float) Invoice::where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'sent')->where('due_date', '<', now()->startOfDay());
            })->sum('amount');

        if ($totalReceivables > 0) {
            $overdueRatio = ($overdueReceivables / $totalReceivables);
            $s5_receivables = $this->clamp((1 - ($overdueRatio * 2)) * 100, 0, 100);
        } else {
            $s5_receivables = 100; // Zero risk
        }

        // -------------------------------------------------------------
        // WEIGHTED COMPOSITE CALCULATION
        // -------------------------------------------------------------
        $totalCompositeScore = (0.25 * $s1_cashflow)
            + (0.20 * $s2_budget)
            + (0.20 * $s3_savings)
            + (0.20 * $s4_emergency)
            + (0.15 * $s5_receivables);

        $totalScore = round($totalCompositeScore);

        // -------------------------------------------------------------
        // TIER / PERCENTILE-BASED LABELING
        // -------------------------------------------------------------
        if ($totalScore >= 85) {
            $grade = 'S';
            $status = 'Optimal';
            $badgeColor = 'emerald';
            $summary = 'Ketahanan finansial prima dengan cadangan likuiditas dan arus kas surplus yang sangat stabil.';
        } elseif ($totalScore >= 70) {
            $grade = 'A';
            $status = 'Sehat';
            $badgeColor = 'lime';
            $summary = 'Kondisi keuangan sehat dan siap menopang fluktuasi order freelance dengan baik.';
        } elseif ($totalScore >= 50) {
            $grade = 'B';
            $status = 'Cukup Stabil';
            $badgeColor = 'amber';
            $summary = 'Struktur finansial cukup aman, namun disarankan mempercepat penagihan piutang dan mempertebal dana darurat.';
        } else {
            $grade = 'C';
            $status = 'Perlu Perhatian';
            $badgeColor = 'rose';
            $summary = 'Arus kas berada pada ambang batas sensitif. Prioritaskan pemotongan biaya non-esensial dan pengamanan dana darurat.';
        }

        return [
            'total_score' => $totalScore,
            'grade' => $grade,
            'status' => $status,
            'badge_color' => $badgeColor,
            'summary' => $summary,
            'rolling_metrics' => [
                'avg_income' => $avg3mIncome,
                'avg_expense' => $avg3mExpense,
                'avg_surplus' => $avg3mSurplus,
                'emergency_months' => round($emergencyMonths, 1),
                'savings_rate' => round($savingsRate, 1),
                'overdue_receivables' => $overdueReceivables,
            ],
            'breakdown' => [
                'cashflow_adequacy' => [
                    'weight' => '25%',
                    'score' => round($s1_cashflow),
                    'weighted_score' => round($s1_cashflow * 0.25, 1),
                    'max' => 25,
                    'label' => 'Cashflow Adequacy & Surplus',
                    'metric_name' => 'Surplus Ratio (3 Bulan)',
                    'value' => round($surplusRatio, 1) . '%',
                    'benchmark' => 'Target >= 40% surplus',
                    'theory' => 'Rolling 3-Month Moving Average',
                ],
                'spending_control' => [
                    'weight' => '20%',
                    'score' => round($s2_budget),
                    'weighted_score' => round($s2_budget * 0.20, 1),
                    'max' => 20,
                    'label' => 'Spending Control & Budget',
                    'metric_name' => 'Kepatuhan Kategori',
                    'value' => $overbudgetCategoriesCount === 0 ? 'Disiplin 100%' : "$overbudgetCategoriesCount Kategori Over",
                    'benchmark' => 'Zero Variance Penalty',
                    'theory' => 'Category Deviation Penalty',
                ],
                'savings_formation' => [
                    'weight' => '20%',
                    'score' => round($s3_savings),
                    'weighted_score' => round($s3_savings * 0.20, 1),
                    'max' => 20,
                    'label' => 'Savings Rate & Capital',
                    'metric_name' => 'Savings Capacity',
                    'value' => round($savingsRate, 1) . '%',
                    'benchmark' => 'Standard CFPB >= 30%',
                    'theory' => 'Capital Formation Rate',
                ],
                'emergency_coverage' => [
                    'weight' => '20%',
                    'score' => round($s4_emergency),
                    'weighted_score' => round($s4_emergency * 0.20, 1),
                    'max' => 20,
                    'label' => 'Emergency Fund Coverage',
                    'metric_name' => 'Ketahanan Kas Likuid',
                    'value' => round($emergencyMonths, 1) . ' Bulan',
                    'benchmark' => 'Gold Standard >= 6 Bulan',
                    'theory' => 'Liquid Reserve Ratio (Capped)',
                ],
                'receivable_risk' => [
                    'weight' => '15%',
                    'score' => round($s5_receivables),
                    'weighted_score' => round($s5_receivables * 0.15, 1),
                    'max' => 15,
                    'label' => 'Receivable & Debt Risk',
                    'metric_name' => 'Piutang Overdue',
                    'value' => $overdueReceivables > 0 ? 'Rp ' . number_format($overdueReceivables, 0, ',', '.') : '0 Overdue (Lancar)',
                    'benchmark' => 'Inverse Overdue Penalty',
                    'theory' => 'Credit Exposure Risk Modeling',
                ],
            ],
        ];
    }

    /**
     * Helper Min-Max clamp
     */
    private function clamp(float $val, float $min, float $max): float
    {
        return max($min, min($max, $val));
    }
}
