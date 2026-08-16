<?php

namespace App\Livewire;

use App\Services\AvailableMoneyService;
use Livewire\Component;

class AvailableMoneyBar extends Component
{
    public float $availableMoney = 0;
    public float $previousAmount = 0;
    public string $direction = 'neutral'; // 'up' | 'down' | 'neutral'

    protected $listeners = [
        'transaction-saved' => 'refresh',
        'refresh-data'      => 'refresh',
    ];

    public function mount(AvailableMoneyService $service): void
    {
        $this->availableMoney = $service->getAvailableMoney(auth()->id());
        $this->previousAmount = $this->availableMoney;
    }

    public function refresh(array $payload = []): void
    {
        $this->previousAmount = $this->availableMoney;

        $service = app(AvailableMoneyService::class);
        $this->availableMoney = $service->getAvailableMoney(auth()->id());

        if ($this->availableMoney > $this->previousAmount) {
            $this->direction = 'up';
        } elseif ($this->availableMoney < $this->previousAmount) {
            $this->direction = 'down';
        } else {
            $this->direction = 'neutral';
        }
    }

    public function render()
    {
        return view('livewire.available-money-bar');
    }
}
