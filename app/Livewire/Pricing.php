<?php

namespace App\Livewire;

use Livewire\Component;

class Pricing extends Component
{
    public string $billingCycle = 'yearly'; // 'monthly' or 'yearly'

    public function render()
    {
        return view('livewire.pricing')
            ->layout('components.layouts.app', [
                'headerTitle' => 'Pilihan Paket & Investasi — PortoFinance PRO',
                'headerSubtitle' => 'Pilih paket terbaik untuk kendali penuh finansial freelance Anda'
            ])
            ->title('Pilihan Paket & Investasi — PortoFinance PRO');
    }
}
