<?php

namespace App\Livewire\Tools;

use App\Models\Account;
use Livewire\Component;

class StudentSplitBill extends Component
{
    public bool $isOpen = false;
    public string $billName = 'Patungan Makan';
    public string $totalAmount = '120000';
    public int $totalPeople = 4;
    public string $taxPercentage = '0';
    public string $tone = 'friendly'; // friendly, recap, talang
    public string $accountInfo = '';
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

        if (empty($this->accountInfo) && auth()->check()) {
            $primaryAcc = Account::where('user_id', auth()->id())
                ->where('is_active', true)
                ->first();
            if ($primaryAcc) {
                $this->accountInfo = "{$primaryAcc->name} " . ($primaryAcc->account_number ?: '');
            }
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
        $accText = !empty(trim($this->accountInfo)) ? "\n💳 Kirim ke: *" . trim($this->accountInfo) . "*" : "";

        if ($this->tone === 'recap') {
            // Nada 2: Alasan lagi rapihin pencatatan keuangan (sangat sopan & formal-santai)
            $text = "Hai {$memberName}! 👋\n\nNiatnya mau sekalian rapihin catatan pengeluaran, ini rincian patungan kita pas *{$this->billName}* yaa:\n💰 Bagianmu: *Rp {$nominal}*{$accText}\n\nNanti kalau lagi senggang boleh langsung transfer/e-wallet yaa biar sama-sama enak & rapi. Makasih banyak yaa! 😊✨";
        } elseif ($this->tone === 'talang') {
            // Nada 3: Alasan sudah ditalangi di kasir (natural & to the point)
            $text = "Halo {$memberName}! 🙏\n\nTadi tagihan *{$this->billName}* udah kutalangi lunas duluan di kasir yaa. Ini rincian per orangnya:\n💰 Bagianmu: *Rp {$nominal}*{$accText}\n\nPas sempat nanti bisa langsung kirim/ganti ke rekening di atas yaa biar pas. Makasih banyak udah seru-seruan bareng! 🙌✨";
        } else {
            // Nada 1 (Default): Super Friendly, Akrab, & Santai (Anti-Gak Enakan)
            $text = "Halo {$memberName}! ✨\n\nSeru banget tadi pas *{$this->billName}*! Btw mau ngabarin rincian patungan kita tadi, bagianmu *Rp {$nominal}* yaa.{$accText}\n\nKalo lagi senggang boleh dioper santai yaa biar dompetku gak boncos hehe 😆. Makasih banyak yaa! 🙏💫";
        }

        return urlencode($text);
    }

    public function getPreviewMessageProperty(): string
    {
        return urldecode($this->getWhatsAppShareText('Budi'));
    }

    public function render()
    {
        return view('livewire.tools.student-split-bill');
    }
}
