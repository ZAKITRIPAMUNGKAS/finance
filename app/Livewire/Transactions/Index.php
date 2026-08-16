<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Category;
use App\Models\Project;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = 'all'; // all, income, expense, transfer
    public string $accountId = 'all';
    public string $categoryId = 'all';
    public string $projectId = 'all';
    public string $month = '';

    protected $listeners = [
        'transaction-saved' => '$refresh',
        'refresh-data' => '$refresh',
    ];

    public function mount()
    {
        $this->month = now()->format('Y-m');
    }

    public function deleteTransaction(int $id)
    {
        $userId = auth()->id();
        $transaction = Transaction::where('user_id', $userId)->findOrFail($id);
        $transaction->delete();
        $this->dispatch('refresh-data');
    }

    public function render()
    {
        $userId = auth()->id();
        $query = Transaction::where('user_id', $userId)
            ->with(['account', 'destinationAccount', 'category', 'project']);

        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        if ($this->accountId !== 'all') {
            $query->where(function ($q) {
                $q->where('account_id', $this->accountId)
                  ->orWhere('destination_account_id', $this->accountId);
            });
        }

        if ($this->categoryId !== 'all') {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->projectId !== 'all') {
            $query->where('project_id', $this->projectId);
        }

        if ($this->month) {
            $query->whereYear('date', substr($this->month, 0, 4))
                  ->whereMonth('date', substr($this->month, 5, 2));
        }

        $transactions = $query->latest('date')->latest('id')->paginate(15);

        // Monthly Totals for filter
        $monthlyQuery = Transaction::where('user_id', $userId);
        if ($this->month) {
            $monthlyQuery->whereYear('date', substr($this->month, 0, 4))
                         ->whereMonth('date', substr($this->month, 5, 2));
        }
        $totalIncome = (float) (clone $monthlyQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (float) (clone $monthlyQuery)->where('type', 'expense')->sum('amount');

        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();
        $categories = Category::where('user_id', $userId)->get();
        $projects = Project::where('user_id', $userId)->latest()->get();

        return view('livewire.transactions.index', compact(
            'transactions',
            'accounts',
            'categories',
            'projects',
            'totalIncome',
            'totalExpense'
        ))->layout('components.layouts.app', [
            'headerTitle' => 'Transactions Ledger',
            'headerSubtitle' => 'Catatan lengkap seluruh transaksi pemasukan, pengeluaran & transfer'
        ]);
    }
}
