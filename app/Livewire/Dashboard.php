<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\PurchaseWishlist;
use App\Models\Subscription;
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

        // 6. Role-Specific Middle Section Data
        // 6a. Freelance Projects (for Freelancer & All)
        $activeProjects = Project::where('user_id', $userId)
            ->with(['client', 'costs', 'invoices'])
            ->whereIn('status', ['prospect', 'in_progress'])
            ->latest()
            ->take(3)
            ->get();

        // 6b. Active Subscriptions (for Employee)
        $activeSubscriptions = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->with('category')
            ->take(4)
            ->get();

        // 6c. Student Budget Category Realization (for Student)
        $studentBudgets = Budget::where('user_id', $userId)
            ->where('period_month', $now->month)
            ->where('period_year', $now->year)
            ->with('category')
            ->take(4)
            ->get()
            ->map(function ($b) use ($userId, $startOfMonth, $endOfMonth) {
                $spent = (float) Transaction::where('user_id', $userId)
                    ->where('category_id', $b->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');
                $b->spent_amount = $spent;
                $limit = (float) $b->fixed_amount_limit;
                $b->percentage_used = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;
                return $b;
            });

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

        // 10. Category Breakdown for Donut Chart
        $categoryBreakdown = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // 11. Role-Adaptive Persona Intelligence Calculations
        $user = auth()->user();
        $daysInMonth = $now->daysInMonth;
        $dayOfMonth = $now->day;
        $remainingDays = max(1, $daysInMonth - $dayOfMonth + 1);
        $safeDailySpend = max(0, $availableMoney / $remainingDays);

        // Employee 50/30/20 breakdown
        $needsExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereHas('category', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereRaw('LOWER(name) LIKE ?', ['%makan%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%kos%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%listrik%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%transport%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%kebutuhan%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%tagihan%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%wifi%']);
                });
            })
            ->sum('amount');

        $lifestyleExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereHas('category', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereRaw('LOWER(name) LIKE ?', ['%jajan%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%nongkrong%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%lifestyle%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%hiburan%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%hobi%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%kopi%']);
                });
            })
            ->sum('amount');

        $needsPct = $monthlyIncome > 0 ? min(100, round(($needsExpense / $monthlyIncome) * 100)) : 0;
        $lifestylePct = $monthlyIncome > 0 ? min(100, round(($lifestyleExpense / $monthlyIncome) * 100)) : 0;
        $savingsPct = max(0, 100 - ($needsPct + $lifestylePct));

        // Merchant Profit & Margin Calculations
        $merchantSales = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereHas('category', fn($q) => $q->where('is_business', true))
            ->sum('amount');

        $merchantCost = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereHas('category', fn($q) => $q->where('is_business', true))
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
            'activeSubscriptions',
            'studentBudgets',
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
