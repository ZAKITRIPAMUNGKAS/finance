<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use App\Models\Transaction;
use Livewire\Component;

class Index extends Component
{
    public bool $isModalOpen = false;
    public ?int $accountId = null;
    public string $name = '';
    public string $type = 'bank';
    public ?string $account_number = null;
    public string $initial_balance = '0';
    public string $color = '#3B82F6';
    public ?string $notes = null;

    // Transfer Modal
    public bool $isTransferModalOpen = false;
    public ?int $from_account_id = null;
    public ?int $to_account_id = null;
    public string $transfer_amount = '';
    public string $transfer_date = '';
    public ?string $transfer_note = null;

    protected $listeners = [
        'refresh-data' => '$refresh',
    ];

    public function mount()
    {
        $this->transfer_date = now()->format('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->reset(['accountId', 'name', 'account_number', 'notes']);
        $this->type = 'bank';
        $this->initial_balance = '0';
        $this->color = '#003B70';
        $this->isModalOpen = true;
    }

    public function selectPreset(string $name, string $type, string $color)
    {
        $this->name = $name;
        $this->type = $type;
        $this->color = $color;
    }

    public function openEditModal(int $id)
    {
        $account = Account::where('user_id', auth()->id())->findOrFail($id);
        $this->accountId = $account->id;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->account_number = $account->account_number;
        $this->initial_balance = (string) $account->initial_balance;
        $this->color = $account->color;
        $this->notes = $account->notes;
        $this->isModalOpen = true;
    }

    public function saveAccount()
    {
        $userId = auth()->id();
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,ewallet,cash,investment,other',
            'account_number' => 'nullable|string|max:100',
            'initial_balance' => 'required|numeric|min:0',
            'color' => 'required|string',
        ]);

        if ($this->accountId) {
            $account = Account::where('user_id', $userId)->findOrFail($this->accountId);
            $account->update([
                'name' => $this->name,
                'type' => $this->type,
                'account_number' => $this->account_number,
                'color' => $this->color,
                'notes' => $this->notes,
            ]);
        } else {
            Account::create([
                'user_id' => $userId,
                'name' => $this->name,
                'type' => $this->type,
                'account_number' => $this->account_number,
                'initial_balance' => $this->initial_balance,
                'current_balance' => $this->initial_balance,
                'color' => $this->color,
                'notes' => $this->notes,
                'is_active' => true,
            ]);
        }

        $this->isModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function openTransferModal()
    {
        $this->transfer_amount = '';
        $this->transfer_note = null;
        $accounts = Account::where('user_id', auth()->id())->where('is_active', true)->get();
        $this->from_account_id = $accounts->first()?->id;
        $this->to_account_id = $accounts->skip(1)->first()?->id;
        $this->isTransferModalOpen = true;
    }

    public function executeTransfer()
    {
        $userId = auth()->id();
        $this->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'transfer_amount' => 'required|numeric|min:1',
            'transfer_date' => 'required|date',
        ]);

        $from = Account::where('user_id', $userId)->findOrFail($this->from_account_id);
        $to = Account::where('user_id', $userId)->findOrFail($this->to_account_id);

        Transaction::create([
            'user_id' => $userId,
            'account_id' => $from->id,
            'destination_account_id' => $to->id,
            'type' => 'transfer',
            'amount' => $this->transfer_amount,
            'date' => $this->transfer_date,
            'description' => 'Transfer dari ' . $from->name . ' ke ' . $to->name,
            'notes' => $this->transfer_note,
        ]);

        $this->isTransferModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function deleteAccount(int $id)
    {
        $userId = auth()->id();
        $account = Account::where('user_id', $userId)->findOrFail($id);
        
        // Prevent deleting if it's the last remaining account
        if (Account::where('user_id', $userId)->where('is_active', true)->count() <= 1) {
            return;
        }

        if ($account->transactions()->count() > 0) {
            $account->update(['is_active' => false]);
        } else {
            $account->delete();
        }

        $this->dispatch('refresh-data');
    }

    public function render()
    {
        $userId = auth()->id();
        $accounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->withCount(['transactions' => fn($q) => $q->where('user_id', $userId)])
            ->get();
            
        $totalBalance = (float) $accounts->sum('current_balance');

        return view('livewire.accounts.index', compact('accounts', 'totalBalance'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Multi-Account & Wallets',
                'headerSubtitle' => 'Kelola rekening bank, e-wallet, cash dompet, dan transfer antar akun'
            ]);
    }
}
