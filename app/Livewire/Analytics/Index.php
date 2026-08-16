<?php

namespace App\Livewire\Analytics;

use App\Models\Category;
use App\Models\Project;
use App\Models\Transaction;
use App\Services\AvailableMoneyService;
use App\Services\FinancialHealthService;
use Livewire\Component;

class Index extends Component
{
    public function render(
        FinancialHealthService $healthService,
        AvailableMoneyService $availableService
    ) {
        $userId = auth()->id();
        $healthData = $healthService->calculateScore($userId);

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // 1. Business vs Lifestyle Spending Breakdown
        $businessExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereHas('category', fn($q) => $q->where('is_business', true))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $personalExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereHas('category', fn($q) => $q->where('is_business', false))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalExpense = $businessExpense + $personalExpense;

        // 2. Project Profitability Leaderboard
        $projects = Project::where('user_id', $userId)
            ->with(['client', 'costs', 'invoices'])
            ->get()
            ->sortByDesc('profit')
            ->take(5);

        // 3. Category distribution
        $categories = Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->withSum(['transactions' => function ($q) use ($userId, $startOfMonth, $endOfMonth) {
                $q->where('user_id', $userId)->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }], 'amount')
            ->get()
            ->filter(fn($c) => ($c->transactions_sum_amount ?? 0) > 0)
            ->sortByDesc('transactions_sum_amount');

        $radarLabels = [
            'Cashflow (25%)',
            'Budgeting (20%)',
            'Savings (20%)',
            'Emergency (20%)',
            'Receivables (15%)'
        ];

        $radarData = [
            $healthData['breakdown']['cashflow_adequacy']['score'] ?? 0,
            $healthData['breakdown']['spending_control']['score'] ?? 0,
            $healthData['breakdown']['savings_formation']['score'] ?? 0,
            $healthData['breakdown']['emergency_coverage']['score'] ?? 0,
            $healthData['breakdown']['receivable_risk']['score'] ?? 0
        ];

        return view('livewire.analytics.index', compact(
            'healthData',
            'radarLabels',
            'radarData',
            'businessExpense',
            'personalExpense',
            'totalExpense',
            'projects',
            'categories'
        ))->layout('components.layouts.app', [
            'headerTitle' => 'Financial Health & Analytics',
            'headerSubtitle' => 'Analisis kesehatan finansial 5 pilar, pemisahan pos bisnis vs personal, & margin proyek'
        ]);
    }
}
