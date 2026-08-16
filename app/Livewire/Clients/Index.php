<?php

namespace App\Livewire\Clients;

use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Transaction;
use Livewire\Component;

class Index extends Component
{
    // Client Modal
    public bool $isClientModalOpen = false;
    public ?int $clientId = null;
    public string $name = '';
    public ?string $company = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $notes = null;

    // Mark Paid Modal
    public bool $isPaidModalOpen = false;
    public ?int $payInvoiceId = null;
    public ?int $payAccountId = null;
    public string $paid_date = '';

    protected $listeners = ['refresh-data' => '$refresh'];

    public function mount()
    {
        $this->paid_date = now()->format('Y-m-d');
        $defaultAccount = Account::where('user_id', auth()->id())->where('is_active', true)->first();
        if ($defaultAccount) {
            $this->payAccountId = $defaultAccount->id;
        }
    }

    public function openCreateClientModal()
    {
        $this->reset(['clientId', 'name', 'company', 'email', 'phone', 'notes']);
        $this->isClientModalOpen = true;
    }

    public function saveClient()
    {
        $userId = auth()->id();
        $this->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($this->clientId) {
            $client = Client::where('user_id', $userId)->findOrFail($this->clientId);
            $client->update([
                'name' => $this->name,
                'company' => $this->company,
                'email' => $this->email,
                'phone' => $this->phone,
                'notes' => $this->notes,
            ]);
        } else {
            Client::create([
                'user_id' => $userId,
                'name' => $this->name,
                'company' => $this->company,
                'email' => $this->email,
                'phone' => $this->phone,
                'notes' => $this->notes,
                'status' => 'active',
            ]);
        }

        $this->isClientModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function openMarkPaidModal(int $invoiceId)
    {
        $invoice = Invoice::whereHas('project', fn($q) => $q->where('user_id', auth()->id()))->findOrFail($invoiceId);
        $this->payInvoiceId = $invoice->id;
        $this->paid_date = now()->format('Y-m-d');
        $this->isPaidModalOpen = true;
    }

    public function confirmMarkPaid()
    {
        $userId = auth()->id();
        $this->validate([
            'payAccountId' => 'required|exists:accounts,id',
            'paid_date' => 'required|date',
        ]);

        $invoice = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->with('project')
            ->findOrFail($this->payInvoiceId);

        $account = Account::where('user_id', $userId)->findOrFail($this->payAccountId);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => $this->paid_date,
            'paid_to_account_id' => $account->id,
        ]);

        // Auto create income transaction
        $incomeCat = Category::where('user_id', $userId)->where('type', 'income')->first();

        Transaction::create([
            'user_id' => $userId,
            'account_id' => $account->id,
            'category_id' => $incomeCat?->id,
            'project_id' => $invoice->project_id,
            'type' => 'income',
            'amount' => $invoice->amount,
            'date' => $this->paid_date,
            'description' => 'Pelunasan Invoice ' . $invoice->invoice_number . ' (' . ($invoice->project->name ?? 'Project') . ')',
        ]);

        $this->isPaidModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function render()
    {
        $userId = auth()->id();
        $clients = Client::where('user_id', $userId)->with(['projects.invoices'])->latest()->get();
        $invoices = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->with(['project.client'])
            ->latest('issue_date')
            ->get();

        $totalReceivables = (float) Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('amount');

        $overdueTotal = (float) Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'sent')->where('due_date', '<', now()->startOfDay());
                  });
            })->sum('amount');

        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();

        return view('livewire.clients.index', compact('clients', 'invoices', 'totalReceivables', 'overdueTotal', 'accounts'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Clients & Invoices / Piutang',
                'headerSubtitle' => 'Kelola direktori klien, invoice penagihan dan status piutang bisnis'
            ]);
    }
}
