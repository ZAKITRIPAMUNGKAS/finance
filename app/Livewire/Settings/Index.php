<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Index extends Component
{
    // Profile Fields
    public string $name = '';
    public string $email = '';
    public ?string $profession = 'Media & IT Freelancer';
    public string $financial_persona = 'freelancer';

    // Password Fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Preferences
    public int $emergency_target_months = 6;
    public string $default_currency = 'IDR';

    public function mount()
    {
        $user = Auth::user() ?? User::first();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->financial_persona = $user->financial_persona ?: 'freelancer';
        }
    }

    public function setPersona(string $personaKey, \App\Services\BudgetAllocationService $budgetService)
    {
        $user = Auth::user() ?? User::first();
        if (!$user) return;

        $this->financial_persona = $personaKey;
        $user->update(['financial_persona' => $personaKey]);

        // Automatically apply relevant budget & category presets
        $budgetService->applyPersonaPreset($user->id, $personaKey, 'stable', 'investment');

        $details = $user->getPersonaDetails();
        session()->flash('persona_success', 'Mode Finansial berhasil disesuaikan ke ' . $details['name'] . '!');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Mode Finansial berhasil diaktifkan: ' . $details['name']
        ]);
        $this->dispatch('refresh-data');
    }

    public function updateProfile()
    {
        $user = Auth::user() ?? User::first();
        if (!$user) return;

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('profile_success', 'Profil akun berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $user = Auth::user() ?? User::first();
        if (!$user) return;

        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password_success', 'Password keamanan berhasil diubah!');
    }

    // Delete Account
    public string $delete_password = '';
    public bool $showDeleteModal = false;

    public function exportAllData()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->load([
            'accounts',
            'categories',
            'transactions.category',
            'transactions.account',
            'projects.client',
            'projects.invoices',
            'clients',
            'wishlists'
        ]);

        $exportData = [
            'app' => 'PortoFinance',
            'version' => '2.0',
            'exported_at' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'subscription_tier' => $user->subscription_tier,
                'created_at' => $user->created_at,
            ],
            'accounts' => $user->accounts,
            'categories' => $user->categories,
            'transactions' => $user->transactions,
            'projects' => $user->projects,
            'clients' => $user->clients,
            'wishlists' => $user->wishlists,
        ];

        $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $filename = 'portofinance-data-backup-' . now()->format('Y-m-d-His') . '.json';

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function confirmDeleteAccount()
    {
        $this->showDeleteModal = true;
        $this->delete_password = '';
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        if (!$user) return;

        if (strtolower($user->email) === 'zakitripamungkas03@gmail.com') {
            session()->flash('data_error', 'Akun Superadmin Utama tidak dapat dihapus.');
            $this->showDeleteModal = false;
            return;
        }

        $this->validate([
            'delete_password' => 'required',
        ]);

        if (!Hash::check($this->delete_password, $user->password)) {
            $this->addError('delete_password', 'Password konfirmasi salah.');
            return;
        }

        // Delete user cascade
        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Akun dan seluruh data Anda telah dihapus secara permanen.');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        $user = Auth::user() ?? User::first();

        return view('livewire.settings.index', compact('user'))
            ->layout('components.layouts.app', [
                'headerTitle' => 'Pengaturan Akun & Preferensi',
                'headerSubtitle' => 'Kelola identitas profil, kata sandi, dan preferensi kalkulasi finansial'
            ]);
    }
}
