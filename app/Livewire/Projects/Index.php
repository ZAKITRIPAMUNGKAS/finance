<?php

namespace App\Livewire\Projects;

use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filterStatus = 'all';
    public string $filterCategory = 'all';
    public string $search = '';

    // Create / Edit Project Modal
    public bool $isProjectModalOpen = false;
    public ?int $projectId = null;
    public ?int $client_id = null;
    public string $name = '';
    public string $category = 'photo_video';
    public ?string $description = null;
    public string $total_revenue = '';
    public ?string $estimated_cost = null;
    public ?string $start_date = null;
    public ?string $deadline = null;
    public string $status = 'in_progress';

    // Add Cost Modal
    public bool $isCostModalOpen = false;
    public ?int $costProjectId = null;
    public string $cost_description = '';
    public string $cost_amount = '';
    public string $cost_date = '';
    public ?int $cost_category_id = null;
    public ?int $cost_account_id = null;
    public bool $deduct_from_account = true;

    // Add Invoice Modal
    public bool $isInvoiceModalOpen = false;
    public ?int $invoiceProjectId = null;
    public string $invoice_number = '';
    public string $invoice_amount = '';
    public string $issue_date = '';
    public string $due_date = '';
    public string $invoice_status = 'sent';
    public ?int $invoice_paid_to_account_id = null;
    public ?string $invoice_notes = null;

    protected $listeners = ['refresh-data' => '$refresh'];

    public function mount()
    {
        $this->cost_date = now()->format('Y-m-d');
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(7)->format('Y-m-d');
    }

    public function openCreateProjectModal()
    {
        if (!auth()->user()->canCreateProject()) {
            $this->dispatch('open-upgrade-modal', feature: 'projects');
            return;
        }

        $this->reset(['projectId', 'name', 'description']);
        $this->total_revenue = '';
        $this->estimated_cost = '';
        $this->category = 'photo_video';
        $this->status = 'in_progress';
        $this->start_date = now()->format('Y-m-d');
        $this->deadline = now()->addDays(14)->format('Y-m-d');
        $firstClient = Client::where('user_id', auth()->id())->first();
        $this->client_id = $firstClient?->id;
        $this->isProjectModalOpen = true;
    }

    public function openEditProjectModal(int $projId)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($projId);
        $this->projectId = $project->id;
        $this->client_id = $project->client_id;
        $this->name = $project->name;
        $this->category = $project->category ?? 'photo_video';
        $this->description = $project->description;
        $this->total_revenue = number_format($project->total_revenue, 0, ',', '.');
        $this->start_date = $project->start_date?->format('Y-m-d');
        $this->deadline = $project->deadline?->format('Y-m-d');
        $this->status = $project->status ?? 'in_progress';
        $this->isProjectModalOpen = true;
    }

    public function deleteProject(int $projId)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($projId);
        $project->delete();
        $this->dispatch('refresh-data');
    }

    public function updatedClientId($val)
    {
        if ($val === 'new_client' || $val === 'add_new') {
            $this->isProjectModalOpen = false;
            return $this->redirect(route('clients'), navigate: true);
        }
    }

    public function saveProject()
    {
        $userId = auth()->id();

        if (!$this->projectId && !auth()->user()->canCreateProject()) {
            $this->isProjectModalOpen = false;
            $this->dispatch('open-upgrade-modal', feature: 'projects');
            session()->flash('error', 'Akun Free Starter dibatasi maksimal 2 proyek. Upgrade ke PRO untuk mencatat proyek tanpa batas.');
            return;
        }

        $this->total_revenue = (string) str_replace(['.', ',', ' '], '', $this->total_revenue);
        if ($this->estimated_cost !== null && $this->estimated_cost !== '') {
            $this->estimated_cost = (string) str_replace(['.', ',', ' '], '', $this->estimated_cost);
        }

        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'total_revenue' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'status' => 'required|in:prospect,in_progress,completed,cancelled',
        ]);

        $cleanRev = (float) $this->total_revenue;

        if ($this->projectId) {
            $project = Project::where('user_id', $userId)->findOrFail($this->projectId);
            $project->update([
                'client_id' => $this->client_id,
                'name' => $this->name,
                'category' => $this->category,
                'description' => $this->description,
                'total_revenue' => $cleanRev,
                'start_date' => $this->start_date,
                'deadline' => $this->deadline,
                'status' => $this->status,
            ]);
        } else {
            Project::create([
                'user_id' => $userId,
                'client_id' => $this->client_id,
                'name' => $this->name,
                'category' => $this->category,
                'description' => $this->description,
                'total_revenue' => $cleanRev,
                'start_date' => $this->start_date,
                'deadline' => $this->deadline,
                'status' => $this->status,
            ]);
        }

        $this->isProjectModalOpen = false;
        $this->dispatch('refresh-data');
    }

    // Add Cost
    public function openAddCostModal(int $projId)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($projId);
        $this->costProjectId = $project->id;
        $this->cost_description = '';
        $this->cost_amount = '';
        $this->cost_date = now()->format('Y-m-d');
        $defaultCat = Category::where('user_id', auth()->id())->where('type', 'expense')->where('is_business', true)->first()
            ?? Category::where('user_id', auth()->id())->where('type', 'expense')->first();
        $this->cost_category_id = $defaultCat?->id;
        $defaultAccount = Account::where('user_id', auth()->id())->where('is_active', true)->first();
        $this->cost_account_id = $defaultAccount?->id;
        $this->deduct_from_account = true;
        $this->isCostModalOpen = true;
    }

    public function saveCost()
    {
        $this->cost_amount = (string) str_replace(['.', ',', ' '], '', $this->cost_amount);

        $this->validate([
            'cost_description' => 'required|string|max:255',
            'cost_amount' => 'required|numeric|min:1',
            'cost_date' => 'required|date',
            'cost_category_id' => 'nullable|exists:categories,id',
            'cost_account_id' => 'nullable|exists:accounts,id',
        ]);

        $project = Project::where('user_id', auth()->id())->findOrFail($this->costProjectId);
        $cleanAmount = (float) $this->cost_amount;

        ProjectCost::create([
            'project_id' => $project->id,
            'category_id' => $this->cost_category_id,
            'description' => $this->cost_description,
            'amount' => $cleanAmount,
            'date' => $this->cost_date,
        ]);

        // If deduct from account, create a corresponding Transaction so balance is updated and visible in transactions
        if ($this->cost_account_id) {
            $account = Account::where('user_id', auth()->id())->find($this->cost_account_id);
            if ($account) {
                Transaction::create([
                    'user_id' => auth()->id(),
                    'account_id' => $account->id,
                    'category_id' => $this->cost_category_id,
                    'project_id' => $project->id,
                    'type' => 'expense',
                    'amount' => $cleanAmount,
                    'date' => $this->cost_date,
                    'description' => "Biaya Project {$project->name}: {$this->cost_description}",
                ]);
            }
        }

        $this->isCostModalOpen = false;
        $this->dispatch('refresh-data');
    }

    // Add Invoice
    public function openAddInvoiceModal(int $projId)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($projId);
        $this->invoiceProjectId = $project->id;
        $this->invoice_number = 'INV-' . date('Ymd') . '-' . rand(100, 999);
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(7)->format('Y-m-d');
        $this->invoice_status = 'sent';
        $this->invoice_notes = null;
        $defaultAccount = Account::where('user_id', auth()->id())->where('is_active', true)->first();
        $this->invoice_paid_to_account_id = $defaultAccount?->id;

        $remaining = max(0, $project->total_revenue - $project->paid_invoices_total);
        $this->invoice_amount = $remaining > 0 ? number_format($remaining, 0, ',', '.') : '';
        $this->isInvoiceModalOpen = true;
    }

    public function saveInvoice()
    {
        $this->invoice_amount = (string) str_replace(['.', ',', ' '], '', $this->invoice_amount);

        if (empty($this->issue_date)) {
            $this->issue_date = now()->format('Y-m-d');
        }
        if (empty($this->due_date)) {
            $this->due_date = now()->addDays(7)->format('Y-m-d');
        }

        $this->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_amount' => 'required|numeric|min:1',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'invoice_status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'invoice_paid_to_account_id' => 'nullable|exists:accounts,id',
        ]);

        $project = Project::where('user_id', auth()->id())->findOrFail($this->invoiceProjectId);
        $cleanAmount = (float) $this->invoice_amount;

        $invoice = Invoice::create([
            'project_id' => $project->id,
            'invoice_number' => $this->invoice_number,
            'amount' => $cleanAmount,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'status' => $this->invoice_status,
            'paid_at' => $this->invoice_status === 'paid' ? now() : null,
            'paid_to_account_id' => $this->invoice_status === 'paid' ? $this->invoice_paid_to_account_id : null,
            'notes' => $this->invoice_notes,
        ]);

        // If marked as paid immediately, create an income transaction and update account balance
        if ($this->invoice_status === 'paid' && $this->invoice_paid_to_account_id) {
            $account = Account::where('user_id', auth()->id())->find($this->invoice_paid_to_account_id);
            if ($account) {
                $cat = Category::firstOrCreate(
                    ['user_id' => auth()->id(), 'name' => 'Pendapatan Project'],
                    ['type' => 'income', 'icon' => 'briefcase', 'color' => '#10B981', 'is_business' => true]
                );

                Transaction::create([
                    'user_id' => auth()->id(),
                    'account_id' => $account->id,
                    'category_id' => $cat->id,
                    'project_id' => $project->id,
                    'type' => 'income',
                    'amount' => $cleanAmount,
                    'date' => $this->issue_date,
                    'description' => "Pembayaran Invoice {$this->invoice_number} ({$project->name})",
                ]);
            }
        }

        $this->isInvoiceModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function markInvoiceAsPaid(int $invoiceId, ?int $accountId = null)
    {
        $invoice = Invoice::whereHas('project', function($q) {
            $q->where('user_id', auth()->id());
        })->with('project')->findOrFail($invoiceId);

        if ($invoice->status === 'paid') return;

        $account = null;
        if ($accountId) {
            $account = Account::where('user_id', auth()->id())->find($accountId);
        } else {
            $account = Account::where('user_id', auth()->id())->where('is_active', true)->first();
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_to_account_id' => $account?->id,
        ]);

        if ($account) {
            $cat = Category::firstOrCreate(
                ['user_id' => auth()->id(), 'name' => 'Pendapatan Project'],
                ['type' => 'income', 'icon' => 'briefcase', 'color' => '#10B981', 'is_business' => true]
            );

            Transaction::create([
                'user_id' => auth()->id(),
                'account_id' => $account->id,
                'category_id' => $cat->id,
                'project_id' => $invoice->project_id,
                'type' => 'income',
                'amount' => $invoice->amount,
                'date' => now()->format('Y-m-d'),
                'description' => "Pelunasan Invoice {$invoice->invoice_number} ({$invoice->project->name})",
            ]);
        }

        $this->dispatch('refresh-data');
    }

    public function markProjectAsPaid(int $projId, ?int $accountId = null)
    {
        $userId = auth()->id();
        $project = Project::where('user_id', $userId)->with('invoices')->findOrFail($projId);

        $account = $accountId 
            ? Account::where('user_id', $userId)->find($accountId)
            : Account::where('user_id', $userId)->where('is_active', true)->first();

        // 1. If project has unpaid invoices, mark them as paid
        $unpaidInvoices = $project->invoices()->where('status', '!=', 'paid')->get();
        if ($unpaidInvoices->count() > 0) {
            foreach ($unpaidInvoices as $inv) {
                $this->markInvoiceAsPaid($inv->id, $account?->id);
            }
        } else {
            // 2. If project has no invoices or remaining balance, record payment
            $remaining = max(0, $project->total_revenue - $project->paid_invoices_total);
            $amountToPay = $remaining > 0 ? $remaining : $project->total_revenue;

            if ($amountToPay > 0) {
                Invoice::create([
                    'project_id' => $project->id,
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . rand(100, 999),
                    'amount' => $amountToPay,
                    'issue_date' => now()->format('Y-m-d'),
                    'due_date' => now()->format('Y-m-d'),
                    'status' => 'paid',
                    'paid_at' => now(),
                    'paid_to_account_id' => $account?->id,
                    'notes' => 'Pelunasan otomatis project ' . $project->name,
                ]);

                if ($account) {
                    $cat = Category::firstOrCreate(
                        ['user_id' => $userId, 'name' => 'Pendapatan Project'],
                        ['type' => 'income', 'icon' => 'briefcase', 'color' => '#10B981', 'is_business' => true]
                    );

                    Transaction::create([
                        'user_id' => $userId,
                        'account_id' => $account->id,
                        'category_id' => $cat->id,
                        'project_id' => $project->id,
                        'type' => 'income',
                        'amount' => $amountToPay,
                        'date' => now()->format('Y-m-d'),
                        'description' => "Pelunasan Project {$project->name}",
                    ]);
                }
            }
        }

        $project->update(['status' => 'completed', 'completed_date' => now()]);
        $this->dispatch('refresh-data');
    }

    public function deleteInvoice(int $invoiceId)
    {
        $invoice = Invoice::whereHas('project', function($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($invoiceId);

        $invoice->delete();
        $this->dispatch('refresh-data');
    }

    public function render()
    {
        $userId = auth()->id();
        $query = Project::where('user_id', $userId)->with(['client', 'costs', 'invoices']);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $projects = $query->latest()->paginate(10);

        // Stats calculation
        $allProjects = Project::where('user_id', $userId)->with(['costs', 'invoices'])->get();
        $totalProjectRevenue = (float) $allProjects->sum('total_revenue');
        $totalProjectCosts = (float) $allProjects->sum(fn($p) => $p->total_cost);
        $totalProjectProfit = $totalProjectRevenue - $totalProjectCosts;
        $avgMargin = $totalProjectRevenue > 0 ? round(($totalProjectProfit / $totalProjectRevenue) * 100, 1) : 0;

        $clients = Client::where('user_id', $userId)->where('status', 'active')->get();
        $categories = Category::where('user_id', $userId)->where('type', 'expense')->get();
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();

        return view('livewire.projects.index', compact(
            'projects',
            'clients',
            'categories',
            'accounts',
            'totalProjectRevenue',
            'totalProjectCosts',
            'totalProjectProfit',
            'avgMargin'
        ))->layout('components.layouts.app', [
            'headerTitle' => 'Freelance Projects & Profit Tracker',
            'headerSubtitle' => 'Pantau margin profit, biaya operasional dan status invoice per project'
        ]);
    }
}
