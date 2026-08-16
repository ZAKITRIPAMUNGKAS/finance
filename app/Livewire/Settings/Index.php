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
        }
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
