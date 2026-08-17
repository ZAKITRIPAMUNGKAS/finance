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

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format alamat email tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'Email atau kata sandi yang Anda masukkan tidak sesuai.');
        $this->dispatch('login-failed');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest', [
                'title' => 'Masuk ke Akun • PortoFinance'
            ]);
    }
}
