<?php

namespace App\Livewire\Auth;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use App\Services\BudgetAllocationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $initial_balance = '5000000';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ];

    public function register(BudgetAllocationService $budgetService)
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $cleanBal = (float) str_replace(['.', ','], ['', '.'], $this->initial_balance);
        if ($cleanBal < 0) {
            $cleanBal = 0;
        }

        // 1. Create Starter Multi-Accounts
        Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'initial_balance' => $cleanBal,
            'current_balance' => $cleanBal,
            'color' => '#003B70',
            'icon' => 'building-2',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'GoPay / E-Wallet',
            'type' => 'ewallet',
            'initial_balance' => 0,
            'current_balance' => 0,
            'color' => '#00AA13',
            'icon' => 'smartphone',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'Cash Dompet',
            'type' => 'cash',
            'initial_balance' => 0,
            'current_balance' => 0,
            'color' => '#F59E0B',
            'icon' => 'banknote',
            'is_active' => true,
        ]);

        // 2. Starter Categories (Personal vs Business)
        Category::create(['user_id' => $user->id, 'name' => 'Project Freelance', 'type' => 'income', 'is_business' => true, 'color' => '#10B981', 'icon' => 'briefcase']);
        Category::create(['user_id' => $user->id, 'name' => 'Monthly Retainer', 'type' => 'income', 'is_business' => true, 'color' => '#059669', 'icon' => 'repeat']);
        Category::create(['user_id' => $user->id, 'name' => 'Passive / Asset', 'type' => 'income', 'is_business' => false, 'color' => '#14B8A6', 'icon' => 'trending-up']);

        Category::create(['user_id' => $user->id, 'name' => 'Equipment & Alat', 'type' => 'expense', 'is_business' => true, 'color' => '#EF4444', 'icon' => 'camera']);
        Category::create(['user_id' => $user->id, 'name' => 'Software & Hosting', 'type' => 'expense', 'is_business' => true, 'color' => '#F97316', 'icon' => 'server']);
        Category::create(['user_id' => $user->id, 'name' => 'Transport & Operasional', 'type' => 'expense', 'is_business' => true, 'color' => '#EAB308', 'icon' => 'car']);
        Category::create(['user_id' => $user->id, 'name' => 'Makan & Minum', 'type' => 'expense', 'is_business' => false, 'color' => '#8B5CF6', 'icon' => 'utensils']);
        Category::create(['user_id' => $user->id, 'name' => 'Listrik, Wifi & Kos', 'type' => 'expense', 'is_business' => false, 'color' => '#EC4899', 'icon' => 'zap']);
        Category::create(['user_id' => $user->id, 'name' => 'Lifestyle & Hiburan', 'type' => 'expense', 'is_business' => false, 'color' => '#6366F1', 'icon' => 'film']);

        // 3. Initialize Adaptive Budget Profile & Groups
        $budgetService->seedInitialBudgetConfiguration($user->id);

        Auth::login($user);
        session()->regenerate();
        session()->flash('new_user_onboarding', true);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.guest', [
                'title' => 'Daftar Akun Baru • PORTO Finance'
            ]);
    }
}
