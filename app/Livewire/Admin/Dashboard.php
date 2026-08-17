<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $totalUsers = User::count();
        $proUsers = User::where('subscription_tier', 'pro')
            ->where(fn($q) => $q->whereNull('subscription_ends_at')->orWhere('subscription_ends_at', '>', now()))
            ->count();
        $lifetimeUsers = User::where('subscription_tier', 'lifetime')->count();
        $trialUsers = User::where('subscription_tier', 'trial')
            ->where(fn($q) => $q->whereNull('trial_ends_at')->orWhere('trial_ends_at', '>', now()))
            ->count();
        $freeUsers = User::where('subscription_tier', 'free')
            ->orWhere(function($q) {
                $q->where('subscription_tier', 'trial')->where('trial_ends_at', '<=', now());
            })
            ->orWhere(function($q) {
                $q->where('subscription_tier', 'pro')->where('subscription_ends_at', '<=', now());
            })
            ->count();
        $bannedUsers = User::where('is_banned', true)->count();

        $totalTransactionsCount = Transaction::count();
        $totalTransactionsVolume = (float) Transaction::sum('amount');
        $totalProjects = Project::count();
        $totalInvoices = Invoice::count();

        $recentUsers = User::latest()->take(8)->get();

        return view('livewire.admin.dashboard', [
            'totalUsers' => $totalUsers,
            'proUsers' => $proUsers,
            'lifetimeUsers' => $lifetimeUsers,
            'trialUsers' => $trialUsers,
            'freeUsers' => $freeUsers,
            'bannedUsers' => $bannedUsers,
            'totalTransactionsCount' => $totalTransactionsCount,
            'totalTransactionsVolume' => $totalTransactionsVolume,
            'totalProjects' => $totalProjects,
            'totalInvoices' => $totalInvoices,
            'recentUsers' => $recentUsers,
        ]);
    }
}
