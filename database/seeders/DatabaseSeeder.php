<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use App\Services\BudgetAllocationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(BudgetAllocationService $budgetService): void
    {
        // 1. Create Default User (Clean Slate)
        $user = User::create([
            'name' => 'Zaki Pratama',
            'email' => 'zaki@example.com',
            'password' => Hash::make('password123'),
            'onboarding_completed' => false,
        ]);

        // 2. Create Starter Multi-Accounts with Rp 0 Balance (Real clean slate)
        Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'account_number' => '8210984123',
            'current_balance' => 0,
            'initial_balance' => 0,
            'color' => '#003B70',
            'icon' => 'building-2',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'account_number' => '081234567890',
            'current_balance' => 0,
            'initial_balance' => 0,
            'color' => '#00AA13',
            'icon' => 'smartphone',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'Dompet Tunai',
            'type' => 'cash',
            'current_balance' => 0,
            'initial_balance' => 0,
            'color' => '#F59E0B',
            'icon' => 'banknote',
            'is_active' => true,
        ]);

        // 3. Initialize Adaptive Budget Profile & Categories
        $budgetService->seedInitialBudgetConfiguration($user->id);
    }
}
