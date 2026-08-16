<?php

use App\Livewire\Accounts\Index as AccountsIndex;
use App\Livewire\Analytics\Index as AnalyticsIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Budgets\Index as BudgetsIndex;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Planning\PurchasePlanning;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Livewire\Wishlists\Index as WishlistsIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Welcome Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 2. Auth Routes
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// 3. Application Core Routes
Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/transactions', TransactionsIndex::class)->name('transactions');
Route::get('/accounts', AccountsIndex::class)->name('accounts');
Route::get('/projects', ProjectsIndex::class)->name('projects');
Route::get('/clients', ClientsIndex::class)->name('clients');
Route::get('/wishlists', WishlistsIndex::class)->name('wishlists');
Route::get('/planning/can-i-afford-this', PurchasePlanning::class)->name('purchase-planning');
Route::get('/budgets', BudgetsIndex::class)->name('budgets');
Route::get('/analytics', AnalyticsIndex::class)->name('analytics');
Route::get('/settings', SettingsIndex::class)->name('settings');
