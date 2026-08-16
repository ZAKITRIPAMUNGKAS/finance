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

    // Add Invoice Modal
    public bool $isInvoiceModalOpen = false;
    public ?int $invoiceProjectId = null;
    public string $invoice_number = '';
    public string $invoice_amount = '';
    public string $issue_date = '';
    public string $due_date = '';
    public string $invoice_status = 'sent';

    protected $listeners = ['refresh-data' => '$refresh'];

    public function mount()
    {
        $this->cost_date = now()->format('Y-m-d');
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(7)->format('Y-m-d');
    }

    public function openCreateProjectModal()
    {
        $this->reset(['projectId', 'name', 'description']);
        $this->total_revenue = '';
        $this->category = 'photo_video';
        $this->status = 'in_progress';
        $this->start_date = now()->format('Y-m-d');
        $this->deadline = now()->addDays(14)->format('Y-m-d');
        $firstClient = Client::where('user_id', auth()->id())->first();
        $this->client_id = $firstClient?->id;
        $this->isProjectModalOpen = true;
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
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'total_revenue' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'status' => 'required|in:prospect,in_progress,completed,cancelled',
        ]);

        if ($this->projectId) {
            $project = Project::where('user_id', $userId)->findOrFail($this->projectId);
            $project->update([
                'client_id' => $this->client_id,
                'name' => $this->name,
                'category' => $this->category,
                'description' => $this->description,
                'total_revenue' => $this->total_revenue,
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
                'total_revenue' => $this->total_revenue,
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
        $defaultCat = Category::where('user_id', auth()->id())->where('type', 'expense')->where('is_business', true)->first();
        $this->cost_category_id = $defaultCat?->id;
        $this->isCostModalOpen = true;
    }

    public function saveCost()
    {
        $this->validate([
            'cost_description' => 'required|string|max:255',
            'cost_amount' => 'required|numeric|min:1',
            'cost_date' => 'required|date',
            'cost_category_id' => 'nullable|exists:categories,id',
        ]);

        $project = Project::where('user_id', auth()->id())->findOrFail($this->costProjectId);

        ProjectCost::create([
            'project_id' => $project->id,
            'category_id' => $this->cost_category_id,
            'description' => $this->cost_description,
            'amount' => $this->cost_amount,
            'date' => $this->cost_date,
        ]);

        $this->isCostModalOpen = false;
        $this->dispatch('refresh-data');
    }

    // Add Invoice
    public function openAddInvoiceModal(int $projId)
    {
        $project = Project::where('user_id', auth()->id())->findOrFail($projId);
        $this->invoiceProjectId = $project->id;
        $this->invoice_number = 'INV-' . date('Y-m') . '-' . rand(100, 999);
        $this->invoice_amount = (string) max(0, $project->total_revenue - $project->paid_invoices_total);
        $this->isInvoiceModalOpen = true;
    }

    public function saveInvoice()
    {
        $this->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_amount' => 'required|numeric|min:1',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'invoice_status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        $project = Project::where('user_id', auth()->id())->findOrFail($this->invoiceProjectId);

        Invoice::create([
            'project_id' => $project->id,
            'invoice_number' => $this->invoice_number,
            'amount' => $this->invoice_amount,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'status' => $this->invoice_status,
        ]);

        $this->isInvoiceModalOpen = false;
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
