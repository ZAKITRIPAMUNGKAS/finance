<?php

namespace App\Livewire\Budgets;

use App\Models\BudgetCategory;
use App\Models\BudgetGroup;
use App\Models\BudgetProfile;
use App\Models\Category;
use App\Services\BudgetAllocationService;
use Livewire\Component;

class Index extends Component
{
    public string $selectedMonth; // 'YYYY-MM'
    
    // Setting / Config Modal State
    public bool $isConfigModalOpen = false;
    public string $profileName = '';
    public string $method = 'floor'; // 'floor' or 'average'
    public array $categoryConfigs = []; // [category_id => ['group_id', 'tier', 'percentage']]
    public array $zScores = []; // [category_id => [status, badge, message]]
    public string $configTab = 'overview'; // 'overview' | 'config'

    protected BudgetAllocationService $budgetService;

    public function boot(BudgetAllocationService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function mount()
    {
        $this->selectedMonth = date('Y-m');
        $this->loadConfiguration();
    }

    public function loadConfiguration()
    {
        $userId = auth()->id();
        $activeProfile = BudgetProfile::with(['budgetCategories'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$activeProfile) {
            $this->budgetService->seedInitialBudgetConfiguration($userId);
            $activeProfile = BudgetProfile::with(['budgetCategories'])
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->first();
        }

        if ($activeProfile) {
            $this->profileName = $activeProfile->name;
            $this->method = $activeProfile->method;

            $this->categoryConfigs = [];
            foreach ($activeProfile->budgetCategories as $bCat) {
                $this->categoryConfigs[$bCat->category_id] = [
                    'id' => $bCat->id,
                    'category_id' => $bCat->category_id,
                    'budget_group_id' => $bCat->budget_group_id,
                    'priority_tier' => (int) $bCat->priority_tier,
                    'target_percentage' => (float) $bCat->target_percentage,
                ];
            }

            $this->calculateAllZScores();
        }
    }

    public function calculateAllZScores()
    {
        $historicalSim = [12.0, 15.0, 10.0, 18.0, 14.0];
        foreach ($this->categoryConfigs as $catId => $cfg) {
            $pct = (float) ($cfg['target_percentage'] ?? 0);
            $this->zScores[$catId] = $this->budgetService->validateZScore($pct, $historicalSim);
        }
    }

    public function openConfigModal()
    {
        $this->loadConfiguration();
        $this->isConfigModalOpen = true;
    }

    public function applyEmaSuggestions()
    {
        $userId = auth()->id();
        $ema = $this->budgetService->calculateEmaSuggestions($userId);
        foreach ($ema as $catId => $suggestedPct) {
            if (isset($this->categoryConfigs[$catId])) {
                $this->categoryConfigs[$catId]['target_percentage'] = $suggestedPct;
            }
        }
        $this->calculateAllZScores();
        session()->flash('config_message', '✨ Saran persentase berbasis EMA historis 6 bulan berhasil diterapkan!');
    }

    public function updatedCategoryConfigs()
    {
        $this->calculateAllZScores();
    }

    public function setMethod(string $method)
    {
        $userId = auth()->id();
        $this->method = in_array($method, ['floor', 'average']) ? $method : 'floor';
        $activeProfile = BudgetProfile::where('user_id', $userId)->where('is_active', true)->first();
        if ($activeProfile) {
            $activeProfile->update(['method' => $this->method]);
        }
        $this->dispatch('refresh-data');
    }

    public function saveConfiguration()
    {
        $userId = auth()->id();
        $totalPct = array_sum(array_column($this->categoryConfigs, 'target_percentage'));
        
        if (abs($totalPct - 100.0) > 0.5) {
            $this->addError('total_percentage', "Total persentase seluruh kategori harus 100%. Saat ini: {$totalPct}%");
            return;
        }

        $activeProfile = BudgetProfile::where('user_id', $userId)->where('is_active', true)->first();
        if ($activeProfile) {
            $activeProfile->update([
                'name' => $this->profileName,
                'method' => $this->method,
            ]);

            foreach ($this->categoryConfigs as $catId => $cfg) {
                BudgetCategory::updateOrCreate(
                    [
                        'budget_profile_id' => $activeProfile->id,
                        'category_id' => $catId,
                    ],
                    [
                        'budget_group_id' => $cfg['budget_group_id'],
                        'priority_tier' => $cfg['priority_tier'],
                        'target_percentage' => (float) $cfg['target_percentage'],
                    ]
                );
            }
        }

        $this->isConfigModalOpen = false;
        session()->flash('message', 'Konfigurasi Budget Allocation Engine berhasil disimpan!');
    }

    public function render()
    {
        $userId = auth()->id();
        $budgetData = $this->budgetService->getBudgetDashboardData($userId, $this->selectedMonth);
        $groups = BudgetGroup::all();
        $categories = Category::where('user_id', $userId)->where('type', 'expense')->get();

        return view('livewire.budgets.index', compact('budgetData', 'groups', 'categories'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Budget Allocation Engine',
                'headerSubtitle' => 'Floor Baseline Method & Adaptive Zero-Based Budgeting (PRD v1.2)'
            ]);
    }
}
