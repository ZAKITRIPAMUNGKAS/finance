<?php

namespace App\Livewire\Auth;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
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

    protected $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format alamat email tidak valid.',
        'email.unique' => 'Email ini sudah terdaftar di PortoFinance.',
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min' => 'Kata sandi minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'onboarding_completed' => false,
        ]);

        // 1. Create Clean Starter Multi-Accounts with Rp 0 Balance
        Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'initial_balance' => 0,
            'current_balance' => 0,
            'color' => '#003B70',
            'icon' => 'building-2',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'initial_balance' => 0,
            'current_balance' => 0,
            'color' => '#00AA13',
            'icon' => 'smartphone',
            'is_active' => true,
        ]);

        Account::create([
            'user_id' => $user->id,
            'name' => 'Dompet Tunai',
            'type' => 'cash',
            'initial_balance' => 0,
            'current_balance' => 0,
            'color' => '#16A34A',
            'icon' => 'wallet',
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        session()->regenerate();
        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.guest', [
                'title' => 'Daftar Akun Baru • PortoFinance'
            ]);
    }
}
