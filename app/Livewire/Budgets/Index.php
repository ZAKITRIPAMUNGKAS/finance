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

    // Smart Financial Persona Survey Modal
    public bool $isSurveyModalOpen = false;
    public int $surveyStep = 1; // 1: Persona, 2: Stability, 3: Priority
    public string $selectedPersona = 'creative_media';
    public string $selectedStability = 'volatile'; // 'volatile' | 'semi' | 'stable'
    public string $selectedPriority = 'emergency'; // 'emergency' | 'wishlist' | 'investment' | 'separate'

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

    public function applyEmaSuggestions()
    {
        $userId = auth()->id();
        $ema = $this->budgetService->calculateEmaSuggestions($userId);
        foreach ($ema as $catId => $item) {
            if (isset($this->categoryConfigs[$catId])) {
                $this->categoryConfigs[$catId]['target_percentage'] = is_array($item) ? (float)($item['suggested_pct'] ?? 0) : (float)$item;
            }
        }
        $this->calculateAllZScores();
    }

    public function openConfigModal()
    {
        $this->loadConfiguration();
        $this->configTab = 'overview';
        $this->isConfigModalOpen = true;
    }

    public function saveConfiguration()
    {
        $userId = auth()->id();
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
                        'target_percentage' => $cfg['target_percentage'],
                    ]
                );
            }
        }

        $this->isConfigModalOpen = false;
        session()->flash('message', 'Konfigurasi Budget Allocation Engine berhasil disimpan!');
    }

    // ── SURVEY & PERSONA METHODS ────────────────────────────────
    public function openSurveyModal()
    {
        $this->surveyStep = 1;
        $this->isSurveyModalOpen = true;
    }

    public function setSurveyStep(int $step)
    {
        $this->surveyStep = $step;
    }

    public function selectSurveyPersona(string $personaKey)
    {
        $this->selectedPersona = $personaKey;
        $this->surveyStep = 2;
    }

    public function selectSurveyStability(string $stability)
    {
        $this->selectedStability = $stability;
        $this->surveyStep = 3;
    }

    public function selectSurveyPriority(string $priority)
    {
        $this->selectedPriority = $priority;
    }

    public function submitSurvey()
    {
        $userId = auth()->id();
        $this->budgetService->applyPersonaPreset(
            $userId, 
            $this->selectedPersona, 
            $this->selectedStability, 
            $this->selectedPriority
        );

        $this->isSurveyModalOpen = false;
        $this->loadConfiguration();
        session()->flash('message', 'Profil Finansial & Alokasi Budget berhasil disesuaikan dengan profesi Anda!');
    }

    public function applyPersonaDirectly(string $personaKey)
    {
        $userId = auth()->id();
        $this->budgetService->applyPersonaPreset($userId, $personaKey);
        $this->isSurveyModalOpen = false;
        $this->loadConfiguration();
        session()->flash('message', 'Preset Alokasi Budget berhasil diterapkan!');
    }

    public function render()
    {
        $userId = auth()->id();
        $budgetData = $this->budgetService->getBudgetDashboardData($userId, $this->selectedMonth);
        $groups = BudgetGroup::all();
        $categories = Category::where('user_id', $userId)->where('type', 'expense')->get();
        $personas = $this->budgetService->getAvailablePersonas();

        return view('livewire.budgets.index', compact('budgetData', 'groups', 'categories', 'personas'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Budget Allocation Engine',
                'headerSubtitle' => 'Floor Baseline Method & Adaptive Zero-Based Budgeting (Universal Engine)',
            ]);
    }
}
