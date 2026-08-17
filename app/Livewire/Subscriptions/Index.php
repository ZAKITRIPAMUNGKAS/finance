<?php

namespace App\Livewire\Subscriptions;

use App\Models\Account;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public bool $isModalOpen = false;
    public ?int $subscriptionId = null;

    public string $name = '';
    public string $amount = '';
    public string $billing_cycle = 'monthly';
    public int $billing_date = 1;
    public ?int $category_id = null;
    public ?int $account_id = null;
    public string $status = 'active';
    public string $icon = 'repeat';
    public string $color = '#0F172A';
    public string $notes = '';

    public string $filterStatus = 'all'; // all, active, paused

    // Preset Catalog
    public array $presets = [
        ['name' => 'ChatGPT Plus', 'amount' => '350000', 'cycle' => 'monthly', 'icon' => 'bot', 'color' => '#10A37F'],
        ['name' => 'Figma Professional', 'amount' => '240000', 'cycle' => 'monthly', 'icon' => 'figma', 'color' => '#F24E1E'],
        ['name' => 'Adobe Creative Cloud', 'amount' => '780000', 'cycle' => 'monthly', 'icon' => 'palette', 'color' => '#FF0000'],
        ['name' => 'Google Workspace', 'amount' => '115000', 'cycle' => 'monthly', 'icon' => 'mail', 'color' => '#4285F4'],
        ['name' => 'GitHub Copilot', 'amount' => '160000', 'cycle' => 'monthly', 'icon' => 'code-2', 'color' => '#24292E'],
        ['name' => 'Canva Pro', 'amount' => '95000', 'cycle' => 'monthly', 'icon' => 'sparkles', 'color' => '#00C4CC'],
        ['name' => 'Cloud Hosting / VPS', 'amount' => '220000', 'cycle' => 'monthly', 'icon' => 'server', 'color' => '#6366F1'],
        ['name' => 'Internet & WiFi', 'amount' => '450000', 'cycle' => 'monthly', 'icon' => 'wifi', 'color' => '#0EA5E9'],
        ['name' => 'Spotify Family', 'amount' => '86000', 'cycle' => 'monthly', 'icon' => 'music', 'color' => '#1DB954'],
        ['name' => 'Netflix Premium', 'amount' => '186000', 'cycle' => 'monthly', 'icon' => 'film', 'color' => '#E50914'],
        ['name' => 'BPJS Kesehatan', 'amount' => '150000', 'cycle' => 'monthly', 'icon' => 'heart-pulse', 'color' => '#16A34A'],
    ];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'billing_cycle' => 'required|in:monthly,yearly,weekly',
            'billing_date' => 'required|integer|min:1|max:31',
            'category_id' => 'nullable|exists:categories,id',
            'account_id' => 'nullable|exists:accounts,id',
            'status' => 'required|in:active,paused,cancelled',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['subscriptionId', 'name', 'amount', 'notes']);
        $this->billing_cycle = 'monthly';
        $this->billing_date = (int) date('j');
        $this->status = 'active';
        $this->icon = 'repeat';
        $this->color = '#0F172A';

        $userId = auth()->id();
        $this->category_id = Category::where('user_id', $userId)->where('type', 'expense')->first()?->id;
        $this->account_id = Account::where('user_id', $userId)->where('is_active', true)->first()?->id;

        $this->isModalOpen = true;
    }

    public function applyPreset(int $index)
    {
        if (isset($this->presets[$index])) {
            $p = $this->presets[$index];
            $this->name = $p['name'];
            $this->amount = $p['amount'];
            $this->billing_cycle = $p['cycle'];
            $this->icon = $p['icon'];
            $this->color = $p['color'];
        }
    }

    public function openEditModal(int $id)
    {
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $this->subscriptionId = $sub->id;
        $this->name = $sub->name;
        $this->amount = (string) $sub->amount;
        $this->billing_cycle = $sub->billing_cycle;
        $this->billing_date = $sub->billing_date;
        $this->category_id = $sub->category_id;
        $this->account_id = $sub->account_id;
        $this->status = $sub->status;
        $this->icon = $sub->icon ?: 'repeat';
        $this->color = $sub->color ?: '#0F172A';
        $this->notes = $sub->notes ?: '';

        $this->isModalOpen = true;
    }

    public function saveSubscription()
    {
        $this->validate();

        $userId = auth()->id();
        $cleanAmount = (float) str_replace(['.', ',', ' '], '', $this->amount);

        if ($this->subscriptionId) {
            $sub = Subscription::where('user_id', $userId)->findOrFail($this->subscriptionId);
            $sub->update([
                'name' => trim($this->name),
                'amount' => $cleanAmount,
                'billing_cycle' => $this->billing_cycle,
                'billing_date' => $this->billing_date,
                'category_id' => $this->category_id,
                'account_id' => $this->account_id,
                'status' => $this->status,
                'icon' => $this->icon,
                'color' => $this->color,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Langganan berhasil diperbarui!');
        } else {
            Subscription::create([
                'user_id' => $userId,
                'name' => trim($this->name),
                'amount' => $cleanAmount,
                'billing_cycle' => $this->billing_cycle,
                'billing_date' => $this->billing_date,
                'category_id' => $this->category_id,
                'account_id' => $this->account_id,
                'status' => $this->status,
                'icon' => $this->icon,
                'color' => $this->color,
                'notes' => $this->notes,
            ]);
            session()->flash('message', 'Langganan baru berhasil ditambahkan!');
        }

        $this->isModalOpen = false;
        $this->dispatch('refresh-data');
    }

    public function toggleStatus(int $id)
    {
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $sub->update([
            'status' => $sub->status === 'active' ? 'paused' : 'active'
        ]);
        $this->dispatch('refresh-data');
    }

    public function deleteSubscription(int $id)
    {
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $sub->delete();
        session()->flash('message', 'Langganan berhasil dihapus.');
        $this->dispatch('refresh-data');
    }

    /**
     * 1-Click Auto-Record as Expense Transaction
     */
    public function recordPayment(int $id)
    {
        $userId = auth()->id();
        $sub = Subscription::where('user_id', $userId)->findOrFail($id);

        if (!$sub->account_id) {
            $sub->account_id = Account::where('user_id', $userId)->where('is_active', true)->first()?->id;
        }

        if (!$sub->account_id) {
            session()->flash('error', 'Silakan tentukan rekening pembayaran terlebih dahulu pada langganan ini.');
            return;
        }

        DB::transaction(function () use ($sub, $userId) {
            Transaction::create([
                'user_id' => $userId,
                'account_id' => $sub->account_id,
                'category_id' => $sub->category_id,
                'type' => 'expense',
                'amount' => $sub->amount,
                'date' => Carbon::today(),
                'description' => 'Pembayaran Langganan: ' . $sub->name,
                'notes' => 'Langganan ' . $sub->name,
                'is_business' => false,
            ]);

            // Update subscription last billed date
            $sub->update(['last_billed_at' => Carbon::today()]);
        });

        session()->flash('message', 'Pembayaran ' . $sub->name . ' senilai Rp ' . number_format($sub->amount, 0, ',', '.') . ' berhasil dibukukan ke transaksi!');
        $this->dispatch('refresh-data');
    }

    public function render()
    {
        $userId = auth()->id();

        $query = Subscription::with(['category', 'account'])
            ->where('user_id', $userId);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $subscriptions = $query->orderBy('billing_date', 'asc')->get();

        // Calculations
        $activeSubs = $subscriptions->where('status', 'active');
        $monthlyBurnRate = $activeSubs->sum(fn($s) => $s->monthly_equivalent);
        $yearlyBurnRate = $monthlyBurnRate * 12;
        $dueSoonCount = $activeSubs->filter(fn($s) => $s->days_remaining >= 0 && $s->days_remaining <= 7)->count();

        $categories = Category::where('user_id', $userId)->where('type', 'expense')->get();
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();

        return view('livewire.subscriptions.index', compact(
            'subscriptions',
            'monthlyBurnRate',
            'yearlyBurnRate',
            'dueSoonCount',
            'categories',
            'accounts'
        ))->layout('components.layouts.app', [
            'title' => 'Subscriptions & Burn Rate — PortoFinance'
        ]);
    }
}
