<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyEmail extends Component
{
    public bool $sent = false;

    public function resendVerification()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        Auth::user()->sendEmailVerificationNotification();
        $this->sent = true;
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
        return view('livewire.auth.verify-email', [
            'user' => Auth::user()
        ])->layout('components.layouts.guest', [
            'title' => 'Verifikasi Email • PortoFinance'
        ]);
    }
}
