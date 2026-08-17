<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Livewire\Accounts\Index as AccountsIndex;
use App\Livewire\AiCopilot\Index as AiCopilotIndex;
use App\Livewire\Analytics\Index as AnalyticsIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Budgets\Index as BudgetsIndex;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Planning\PurchasePlanning;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Subscriptions\Index as SubscriptionsIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Livewire\Wishlists\Index as WishlistsIndex;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Welcome Page (Direct to Dashboard if Authenticated)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// 2. Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    
    // Google OAuth Routes
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

// Logout Route (Authenticated)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// 3. Email Verification Notice & Verification Handler
Route::get('/email/verify', VerifyEmail::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 4. Authenticated & Verified Core Application Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transactions', TransactionsIndex::class)->name('transactions');
    Route::get('/accounts', AccountsIndex::class)->name('accounts');
    Route::get('/subscriptions', SubscriptionsIndex::class)->name('subscriptions');
    Route::get('/projects', ProjectsIndex::class)->name('projects');
    Route::get('/clients', ClientsIndex::class)->name('clients');
    Route::get('/wishlists', WishlistsIndex::class)->name('wishlists');
    Route::get('/planning/can-i-afford-this', PurchasePlanning::class)->name('purchase-planning');
    Route::get('/budgets', BudgetsIndex::class)->name('budgets');
    Route::get('/analytics', AnalyticsIndex::class)->name('analytics');
    Route::get('/ai-copilot', AiCopilotIndex::class)->name('ai-copilot');
    Route::get('/settings', SettingsIndex::class)->name('settings');

    // Invoices & Financial Reports
    Route::get('/invoices/{id}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/reports/financial-statement', \App\Livewire\Reports\FinancialStatement::class)->name('reports.financial-statement');
    Route::get('/reports/export-transactions-csv', [\App\Http\Controllers\ReportExportController::class, 'exportTransactionsCsv'])->name('reports.export-csv');
});

// 5. Public Invoice Share Link (For Clients)
Route::get('/i/{hash}', [\App\Http\Controllers\InvoiceController::class, 'publicView'])->name('invoices.public');

// 6. Superadmin Command Center Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/users', \App\Livewire\Admin\Users\Index::class)->name('users');
});

// Leave Impersonation Route
Route::get('/admin/leave-impersonation', function () {
    if (session()->has('admin_impersonator_id')) {
        $adminId = session()->pull('admin_impersonator_id');
        $admin = \App\Models\User::find($adminId);
        if ($admin && $admin->isAdmin()) {
            Auth::login($admin);
            return redirect()->route('admin.users')->with('success', 'Kembali ke sesi Superadmin.');
        }
    }
    return redirect()->route('dashboard');
})->middleware('auth')->name('admin.leave-impersonation');

