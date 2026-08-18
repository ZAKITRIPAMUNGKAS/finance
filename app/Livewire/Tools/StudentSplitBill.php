<?php

namespace App\Livewire\Tools;

use Livewire\Component;

class StudentSplitBill extends Component
{
    public bool $isOpen = false;
    public string $billName = 'Patungan Makan';
    public string $totalAmount = '120000';
    public int $totalPeople = 4;
    public string $taxPercentage = '0';
    public array $members = [];

    protected $listeners = [
        'open-split-bill' => 'open',
    ];

    public function open()
    {
        $this->isOpen = true;
        if (empty($this->members)) {
            $this->initMembers();
        }
    }

    public function updatedTotalPeople()
    {
        $this->totalPeople = max(2, min(50, (int) $this->totalPeople));
        $this->initMembers();
    }

    public function initMembers()
    {
        $this->members = [];
        for ($i = 1; $i <= $this->totalPeople; $i++) {
            $this->members[] = [
                'name' => $i === 1 ? 'Saya (Talang dulu)' : 'Teman ' . ($i - 1),
                'phone' => '',
                'is_paid' => $i === 1,
            ];
        }
    }

    public function togglePaid(int $index)
    {
        if (isset($this->members[$index])) {
            $this->members[$index]['is_paid'] = !$this->members[$index]['is_paid'];
        }
    }

    public function getPerPersonAmountProperty(): float
    {
        $cleanTotal = (float) preg_replace('/[^\d]/', '', (string) $this->totalAmount);
        $tax = (float) $this->taxPercentage;
        $totalWithTax = $cleanTotal * (1 + ($tax / 100));
        
        $people = max(1, $this->totalPeople);
        return ceil($totalWithTax / $people);
    }

    public function getWhatsAppShareText(string $memberName): string
    {
        $nominal = number_format($this->perPersonAmount, 0, ',', '.');
        $text = "Halo {$memberName}! 👋\n\nIni rincian patungan *{$this->billName}*:\n💰 Nominal kamu: *Rp {$nominal}*\n\nBisa langsung transfer/kirim e-wallet ya. Terima kasih banyak! ✨";
        return urlencode($text);
    }

    public function render()
    {
        return view('livewire.tools.student-split-bill');
    }
}
