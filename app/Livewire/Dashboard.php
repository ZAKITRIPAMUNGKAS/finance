<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\PurchaseWishlist;
use App\Models\Transaction;
use App\Services\AvailableMoneyService;
use App\Services\FinancialHealthService;
use App\Services\SavingPlanService;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    protected $listeners = [
        'transaction-saved'  => '$refresh',
        'refresh-data'       => '$refresh',
        'wishlist-updated'   => '$refresh',
        'saving-recorded'    => '$refresh',
        'project-updated'    => '$refresh',
    ];

    public function render(
        AvailableMoneyService $availableMoneyService,
        FinancialHealthService $financialHealthService,
        SavingPlanService $savingPlanService
    ) {
        $userId = auth()->id();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Balance & Available Money Metrics
        $totalBalance = $availableMoneyService->getTotalBalance($userId);
        $wishlistLocked = $availableMoneyService->getTotalWishlistAllocated($userId);
        $availableMoney = $availableMoneyService->getAvailableMoney($userId);
        $emergencyMonths = $availableMoneyService->getEmergencyFundMonths($userId);

        // 2. Current Month Cashflow
        $monthlyIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netCashflow = $monthlyIncome - $monthlyExpense;

        // 3. Financial Health Score
        $healthScore = $financialHealthService->calculateScore($userId);

        // 4. Accounts List
        $accounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        // 5. Active Wishlists with Saving Feasibility Evaluation
        $activeWishlists = PurchaseWishlist::where('user_id', $userId)
            ->whereIn('status', ['planning', 'saving', 'ready'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->take(4)
            ->get()
            ->map(function ($w) use ($savingPlanService, $userId) {
                $w->plan_eval = $savingPlanService->evaluateItemPlan($w, $userId);
                return $w;
            });

        // 6. Active Freelance Projects
        $activeProjects = Project::where('user_id', $userId)
            ->with(['client', 'costs', 'invoices'])
            ->whereIn('status', ['prospect', 'in_progress'])
            ->latest()
            ->take(3)
            ->get();

        // 7. Unpaid Invoices Summary (Scoped to user's projects)
        $unpaidInvoicesTotal = (float) Invoice::whereHas('project', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('amount');

        $overdueInvoicesCount = Invoice::whereHas('project', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'sent')->where('due_date', '<', now()->startOfDay());
                  });
            })
            ->count();

        // 8. Recent Transactions
        $recentTransactions = Transaction::where('user_id', $userId)
            ->with(['account', 'category', 'project'])
            ->latest('date')
            ->latest('id')
            ->take(8)
            ->get();

        // 9. Monthly Cashflow Chart Data (Last 6 Months)
        $chartLabels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthStart = $monthDate->copy()->startOfMonth();
            $monthEnd = $monthDate->copy()->endOfMonth();

            $chartLabels[] = $monthDate->translatedFormat('M Y');
            $incomeData[] = (float) Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            $expenseData[] = (float) Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
        }

        // 10. Category Spending Breakdown (Current Month)
        $categoryBreakdown = Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->withSum(['transactions' => function ($q) use ($userId, $startOfMonth, $endOfMonth) {
                $q->where('user_id', $userId)
                  ->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }], 'amount')
            ->get()
            ->filter(fn($c) => ($c->transactions_sum_amount ?? 0) > 0)
            ->sortByDesc('transactions_sum_amount');

        // 11. Role-Specific Metric Calculations
        $user = auth()->user();
        $daysInMonth = Carbon::now()->daysInMonth;
        $dayOfMonth = Carbon::now()->day;
        $remainingDays = max(1, $daysInMonth - $dayOfMonth + 1);

        // Student Safe Daily Spend: (Uang Tersedia) / Sisa Hari
        $safeDailySpend = max(0, round($availableMoney / $remainingDays));

        // Employee 50/30/20 Breakdown
        $effectiveIncome = $monthlyIncome > 0 ? $monthlyIncome : max(1, $monthlyExpense);
        $needsExpense = $categoryBreakdown->filter(fn($c) => in_array(strtolower($c->name), ['makan', 'makan & belanja dapur', 'sewa kos', 'sewa/cicilan', 'listrik', 'transport', 'transportasi']))->sum('transactions_sum_amount');
        $lifestyleExpense = $categoryBreakdown->filter(fn($c) => in_array(strtolower($c->name), ['lifestyle', 'nongkrong', 'hobi', 'hiburan', 'jajan']))->sum('transactions_sum_amount');
        $savingsAllocated = $wishlistLocked;

        $needsPct = round(($needsExpense / $effectiveIncome) * 100);
        $lifestylePct = round(($lifestyleExpense / $effectiveIncome) * 100);
        $savingsPct = round(($savingsAllocated / $effectiveIncome) * 100);

        // Merchant Business Cashflow
        $merchantSales = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $merchantCost = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $merchantProfit = $merchantSales - $merchantCost;
        $merchantMarginPct = $merchantSales > 0 ? round(($merchantProfit / $merchantSales) * 100, 1) : 0;

        return view('livewire.dashboard', compact(
            'totalBalance',
            'wishlistLocked',
            'availableMoney',
            'emergencyMonths',
            'monthlyIncome',
            'monthlyExpense',
            'netCashflow',
            'healthScore',
            'accounts',
            'activeWishlists',
            'activeProjects',
            'unpaidInvoicesTotal',
            'overdueInvoicesCount',
            'recentTransactions',
            'chartLabels',
            'incomeData',
            'expenseData',
            'categoryBreakdown',
            'user',
            'remainingDays',
            'safeDailySpend',
            'needsExpense',
            'lifestyleExpense',
            'needsPct',
            'lifestylePct',
            'savingsPct',
            'merchantSales',
            'merchantCost',
            'merchantProfit',
            'merchantMarginPct'
        ))->layout('components.layouts.app', [
            'headerTitle' => 'Financial Control Center',
            'headerSubtitle' => 'Ringkasan Cashflow Pribadi, Profit Bisnis & Wishlist Saving'
        ]);
    }
}
