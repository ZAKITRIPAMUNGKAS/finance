<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = true;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'Email atau password yang Anda masukkan salah.');
        $this->dispatch('login-failed');
    }

    public function quickDemoLogin()
    {
        $this->email = 'zaki@example.com';
        $this->password = 'password123';
        $this->login();
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest', [
                'title' => 'Masuk ke Akun • PORTO Finance'
            ]);
    }
}
