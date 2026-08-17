<?php

namespace App\Livewire\Reports;

use App\Models\Account;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;

class FinancialStatement extends Component
{
    public int $month;
    public int $year;

    // Modal view for invoice
    public bool $isInvoiceModalOpen = false;
    public ?Invoice $selectedInvoice = null;

    public function mount()
    {
        $this->month = (int) request('month', date('n'));
        $this->year = (int) request('year', date('Y'));
    }

    public function viewInvoice(int $invoiceId)
    {
        $this->selectedInvoice = Invoice::whereHas('project', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['project.client', 'project.user'])
            ->find($invoiceId);

        if ($this->selectedInvoice) {
            $this->isInvoiceModalOpen = true;
        }
    }

    public function closeInvoiceModal()
    {
        $this->isInvoiceModalOpen = false;
        $this->selectedInvoice = null;
    }

    public function render()
    {
        $userId = auth()->id();
        $user = auth()->user();

        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Incomes
        $incomeTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'account', 'project'])
            ->orderBy('date', 'desc')
            ->get();
        $totalIncome = (float) $incomeTransactions->sum('amount');

        // 2. Expenses
        $expenseTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'account'])
            ->orderBy('date', 'desc')
            ->get();
        $totalExpense = (float) $expenseTransactions->sum('amount');

        // Expenses by category
        $expensesByCategory = $expenseTransactions->groupBy(fn($t) => $t->category?->name ?? 'Lain-lain')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc();

        // 3. Net Profit & Margin
        $netProfit = $totalIncome - $totalExpense;
        $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0;

        // 4. Accounts & Cash
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();
        $totalCash = (float) $accounts->sum('current_balance');

        // 5. Active Subscriptions
        $subscriptions = Subscription::where('user_id', $userId)->where('status', 'active')->get();
        $monthlyBurn = (float) $subscriptions->sum(fn($s) => $s->monthly_equivalent);

        // 6. Invoices in this month
        $invoices = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['project.client'])
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('livewire.reports.financial-statement', compact(
            'user',
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'profitMargin',
            'expensesByCategory',
            'incomeTransactions',
            'expenseTransactions',
            'accounts',
            'totalCash',
            'monthlyBurn',
            'invoices'
        ))->layout('components.layouts.app');
    }
}
