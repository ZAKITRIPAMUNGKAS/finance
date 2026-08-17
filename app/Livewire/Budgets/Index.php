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
    public array $categoryConfigs = []; // [category_id => ['id', 'category_id', 'name', 'budget_group_id', 'priority_tier', 'target_percentage']]
    public array $zScores = []; // [category_id => [status, badge, message]]
    public string $configTab = 'overview'; // 'overview' | 'config'

    // Add New Custom Pos Kategori State
    public bool $isAddingCategory = false;
    public string $newCategoryName = '';
    public ?int $newCategoryGroupId = null;
    public int $newCategoryTier = 1;
    public float $newCategoryPercentage = 5.0;

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
        $activeProfile = BudgetProfile::with(['budgetCategories.category'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$activeProfile) {
            $this->budgetService->seedInitialBudgetConfiguration($userId);
            $activeProfile = BudgetProfile::with(['budgetCategories.category'])
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
                    'name' => $bCat->category?->name ?? 'Kategori',
                    'budget_group_id' => $bCat->budget_group_id,
                    'priority_tier' => (int) $bCat->priority_tier,
                    'target_percentage' => (float) $bCat->target_percentage,
                ];
            }

            $this->calculateAllZScores();
        }

        $defaultGroup = BudgetGroup::first();
        if ($defaultGroup && !$this->newCategoryGroupId) {
            $this->newCategoryGroupId = $defaultGroup->id;
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

    public function autoBalanceAllocation()
    {
        $total = array_sum(array_column($this->categoryConfigs, 'target_percentage'));
        if ($total <= 0) return;

        $runningSum = 0;
        $keys = array_keys($this->categoryConfigs);
        $lastKey = end($keys);

        foreach ($this->categoryConfigs as $catId => &$cfg) {
            if ($catId === $lastKey) {
                $cfg['target_percentage'] = round(max(0, 100.0 - $runningSum), 1);
            } else {
                $scaled = round(($cfg['target_percentage'] / $total) * 100.0, 1);
                $cfg['target_percentage'] = $scaled;
                $runningSum += $scaled;
            }
        }
        unset($cfg);
        $this->calculateAllZScores();
    }

    public function openConfigModal()
    {
        $this->loadConfiguration();
        $this->isAddingCategory = false;
        $this->configTab = 'overview';
        $this->isConfigModalOpen = true;
    }

    public function toggleAddCategory()
    {
        $this->isAddingCategory = !$this->isAddingCategory;
        if ($this->isAddingCategory && empty($this->newCategoryGroupId)) {
            $this->newCategoryGroupId = BudgetGroup::first()?->id;
        }
    }

    public function addNewCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:100',
            'newCategoryGroupId' => 'required|exists:budget_groups,id',
            'newCategoryTier' => 'required|in:1,2,3',
            'newCategoryPercentage' => 'required|numeric|min:0|max:100',
        ]);

        $userId = auth()->id();
        $cleanName = trim($this->newCategoryName);

        $category = Category::firstOrCreate(
            [
                'user_id' => $userId,
                'name' => $cleanName,
                'type' => 'expense',
            ],
            [
                'color' => '#0F172A',
                'icon' => 'tag',
            ]
        );

        $this->categoryConfigs[$category->id] = [
            'id' => null,
            'category_id' => $category->id,
            'name' => $category->name,
            'budget_group_id' => (int) $this->newCategoryGroupId,
            'priority_tier' => (int) $this->newCategoryTier,
            'target_percentage' => (float) $this->newCategoryPercentage,
        ];

        $this->newCategoryName = '';
        $this->newCategoryPercentage = 5.0;
        $this->isAddingCategory = false;
        $this->calculateAllZScores();
    }

    public function removeCategory(int $catId)
    {
        unset($this->categoryConfigs[$catId]);
        $this->calculateAllZScores();
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

            $savedCategoryIds = [];

            foreach ($this->categoryConfigs as $catId => $cfg) {
                // Update category name in database if user edited it
                if (!empty($cfg['name'])) {
                    Category::where('id', $catId)
                        ->where('user_id', $userId)
                        ->update(['name' => trim($cfg['name'])]);
                }

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

                $savedCategoryIds[] = $catId;
            }

            // Remove any budget categories that were deleted from the config
            BudgetCategory::where('budget_profile_id', $activeProfile->id)
                ->whereNotIn('category_id', $savedCategoryIds)
                ->delete();
        }

        $this->isConfigModalOpen = false;
        $this->loadConfiguration();
        session()->flash('message', 'Konfigurasi persentase budget & pos kategori berhasil disimpan!');
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
        session()->flash('message', 'Profil Finansial & Alokasi Budget berhasil disesuaikan dengan arketipe Anda!');
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
                'headerTitle' => 'Smart Budget Engine',
                'headerSubtitle' => 'Universal Adaptive Zero-Based Budgeting & Percentage Optimizer',
            ]);
    }
}
