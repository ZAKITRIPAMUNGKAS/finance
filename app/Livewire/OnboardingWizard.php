<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Category;
use App\Models\IncomeFloorSnapshot;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BudgetAllocationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OnboardingWizard extends Component
{
    public bool $isOpen = false;
    public int $step = 1;

    // Step 1: Persona
    public string $persona = 'employee_salary';

    // Step 2: Selected Accounts & Real Balances
    public array $activeAccounts = [
        'bca' => true,
        'gopay' => true,
        'cash' => true,
    ];

    public array $accountBalances = [
        'bca' => '0',
        'mandiri' => '0',
        'bri' => '0',
        'bni' => '0',
        'jago' => '0',
        'seabank' => '0',
        'gopay' => '0',
        'ovo' => '0',
        'dana' => '0',
        'shopeepay' => '0',
        'cash' => '0',
    ];

    public array $accountNumbers = [
        'bca' => '',
        'mandiri' => '',
        'bri' => '',
        'bni' => '',
        'jago' => '',
        'seabank' => '',
        'gopay' => '',
        'ovo' => '',
        'dana' => '',
        'shopeepay' => '',
        'cash' => '',
    ];

    // Step 3: Estimasi Pemasukan Bulanan
    public string $monthlyIncome = '5000000';

    protected $listeners = [
        'open-onboarding' => 'open',
    ];

    public function mount()
    {
        $user = Auth::user();
        if ($user && !$user->onboarding_completed) {
            // Check if user has no accounts or explicitly hasn't completed onboarding
            $this->isOpen = true;
        }
    }

    public function open()
    {
        $this->step = 1;
        $this->isOpen = true;
    }

    public function setPersona(string $personaKey)
    {
        $this->persona = $personaKey;
    }

    public function toggleAccount(string $key)
    {
        if (isset($this->activeAccounts[$key])) {
            unset($this->activeAccounts[$key]);
        } else {
            $this->activeAccounts[$key] = true;
        }
    }

    public function setMonthlyIncomeChip(string $amount)
    {
        $this->monthlyIncome = $amount;
    }

    public function nextStep()
    {
        if ($this->step === 2) {
            // Ensure at least 1 account is active
            if (empty($this->activeAccounts)) {
                $this->activeAccounts['cash'] = true;
            }
        }
        $this->step++;
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function saveOnboarding(array $payload, BudgetAllocationService $budgetService)
    {
        if (isset($payload['persona'])) {
            $this->persona = $payload['persona'];
        }
        if (isset($payload['activeAccounts'])) {
            $this->activeAccounts = $payload['activeAccounts'];
        }
        if (isset($payload['accountBalances'])) {
            $this->accountBalances = $payload['accountBalances'];
        }
        if (isset($payload['monthlyIncome'])) {
            $this->monthlyIncome = (string) $payload['monthlyIncome'];
        }

        return $this->completeOnboarding($budgetService);
    }

    public function completeOnboarding(BudgetAllocationService $budgetService)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Account Catalog definitions with official names & types
        $catalog = [
            'bca' => ['name' => 'BCA Utama', 'type' => 'bank', 'color' => '#003B70', 'icon' => 'building-2'],
            'mandiri' => ['name' => 'Bank Mandiri', 'type' => 'bank', 'color' => '#002D62', 'icon' => 'landmark'],
            'bri' => ['name' => 'Bank BRI', 'type' => 'bank', 'color' => '#00529C', 'icon' => 'building-2'],
            'bni' => ['name' => 'Bank BNI', 'type' => 'bank', 'color' => '#F15A24', 'icon' => 'landmark'],
            'jago' => ['name' => 'Bank Jago', 'type' => 'bank', 'color' => '#845EC2', 'icon' => 'credit-card'],
            'seabank' => ['name' => 'SeaBank', 'type' => 'bank', 'color' => '#FF5722', 'icon' => 'smartphone'],
            'gopay' => ['name' => 'GoPay', 'type' => 'ewallet', 'color' => '#00AA13', 'icon' => 'smartphone'],
            'ovo' => ['name' => 'OVO', 'type' => 'ewallet', 'color' => '#4C3494', 'icon' => 'smartphone'],
            'dana' => ['name' => 'DANA', 'type' => 'ewallet', 'color' => '#118EEA', 'icon' => 'smartphone'],
            'shopeepay' => ['name' => 'ShopeePay', 'type' => 'ewallet', 'color' => '#EE4D2D', 'icon' => 'shopping-bag'],
            'cash' => ['name' => 'Dompet Tunai', 'type' => 'cash', 'color' => '#F59E0B', 'icon' => 'banknote'],
        ];

        // 1. Save or Update Accounts
        foreach ($this->activeAccounts as $key => $isActive) {
            if (!$isActive || !isset($catalog[$key])) {
                continue;
            }

            $meta = $catalog[$key];
            $cleanBal = (float) str_replace(['.', ',', ' '], '', $this->accountBalances[$key] ?? '0');
            if ($cleanBal < 0) {
                $cleanBal = 0;
            }

            $accNum = trim($this->accountNumbers[$key] ?? '');

            Account::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $meta['name'],
                ],
                [
                    'type' => $meta['type'],
                    'account_number' => $accNum ?: null,
                    'initial_balance' => $cleanBal,
                    'current_balance' => $cleanBal,
                    'color' => $meta['color'],
                    'icon' => $meta['icon'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Clean up inactive default accounts if they have 0 balance & no transactions
        $selectedNames = collect($this->activeAccounts)->keys()->map(fn($k) => $catalog[$k]['name'] ?? null)->filter()->toArray();
        Account::where('user_id', $user->id)
            ->whereNotIn('name', $selectedNames)
            ->where('current_balance', 0)
            ->doesntHave('transactions')
            ->delete();

        // 3. Apply Persona Presets (Income & Expense Categories tailored to role)
        $budgetService->applyPersonaPreset($user->id, $this->persona, 'stable', 'investment');

        // 4. Set Baseline Income Floor Snapshot
        $cleanIncome = (float) str_replace(['.', ',', ' '], '', $this->monthlyIncome ?: '5000000');
        if ($cleanIncome <= 0) {
            $cleanIncome = 5000000.0;
        }

        $nowMonth = Carbon::now()->format('Y-m');
        IncomeFloorSnapshot::updateOrCreate(
            [
                'user_id' => $user->id,
                'month' => $nowMonth,
            ],
            [
                'income_floor_value' => $cleanIncome,
                'cv_value' => 0.15,
                'method_selected' => 'average',
                'avg_income' => $cleanIncome,
                'std_income' => 0,
            ]
        );

        // 5. Mark Onboarding as Completed
        $user->update(['onboarding_completed' => true]);

        $this->isOpen = false;
        $this->dispatch('refresh-data');
        session()->flash('onboarding_success', 'Selamat datang! Keuangan Anda telah siap dikelola.');

        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding-wizard');
    }
}
