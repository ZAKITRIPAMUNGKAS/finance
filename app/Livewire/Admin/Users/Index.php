<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Services\BudgetAllocationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTier = 'all'; // all, trial, free, pro, lifetime, banned, admin
    public string $filterPersona = 'all'; // all, student, employee, merchant, freelancer, all_in_one

    // Modal state for changing user tier / details / persona
    public bool $showEditModal = false;
    public ?int $selectedUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editRole = 'user';
    public string $editPersona = 'freelancer';
    public string $editTier = 'trial';
    public ?int $extendDays = 30;
    public bool $editIsBanned = false;
    public string $editBannedReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterTier' => ['except' => 'all'],
        'filterPersona' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTier()
    {
        $this->resetPage();
    }

    public function updatingFilterPersona()
    {
        $this->resetPage();
    }

    public function openEditModal(int $userId)
    {
        $user = User::findOrFail($userId);
        $this->selectedUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->role;
        $this->editPersona = $user->financial_persona ?? 'freelancer';
        $this->editTier = $user->subscription_tier;
        $this->editIsBanned = (bool) $user->is_banned;
        $this->editBannedReason = $user->banned_reason ?? '';
        $this->extendDays = 30;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['selectedUserId', 'editName', 'editEmail', 'editRole', 'editPersona', 'editTier', 'editIsBanned', 'editBannedReason', 'extendDays']);
    }

    public function saveUserChanges(BudgetAllocationService $budgetService)
    {
        if (!$this->selectedUserId) return;

        $user = User::findOrFail($this->selectedUserId);

        // Prevent demoting primary admin or own superadmin access
        if ((strtolower($user->email) === 'zakitripamungkas03@gmail.com' || $user->id === auth()->id()) && $this->editRole !== 'admin') {
            session()->flash('error', 'Akun Superadmin Utama tidak dapat diturunkan hak aksesnya.');
            return;
        }

        $oldPersona = $user->financial_persona;

        $user->role = $this->editRole;
        $user->financial_persona = $this->editPersona;
        $user->subscription_tier = $this->editTier;
        $user->is_banned = $this->editIsBanned;
        $user->banned_reason = $this->editIsBanned ? ($this->editBannedReason ?: 'Pelanggaran ketentuan penggunaan.') : null;

        if ($this->editTier === 'pro') {
            $user->subscription_ends_at = $this->extendDays ? now()->addDays($this->extendDays) : now()->addMonth();
        } elseif ($this->editTier === 'trial') {
            $user->trial_ends_at = $this->extendDays ? now()->addDays($this->extendDays) : now()->addDays(14);
        } elseif ($this->editTier === 'lifetime') {
            $user->subscription_ends_at = null;
            $user->trial_ends_at = null;
        } elseif ($this->editTier === 'free') {
            $user->subscription_ends_at = null;
            $user->trial_ends_at = null;
        }

        $user->save();

        if ($oldPersona !== $this->editPersona) {
            $budgetService->applyPersonaPreset($user->id, $this->editPersona);
        }

        $this->closeEditModal();
        session()->flash('success', "Data peran, paket, dan profil profesi {$user->name} berhasil diperbarui.");
    }

    public function setPersonaDirect(int $userId, string $persona, BudgetAllocationService $budgetService)
    {
        $user = User::findOrFail($userId);
        $user->financial_persona = $persona;
        $user->save();

        $budgetService->applyPersonaPreset($user->id, $persona);

        $labels = [
            'student' => 'Pelajar & Mahasiswa',
            'employee' => 'Karyawan & Kantoran',
            'merchant' => 'Pedagang & UMKM',
            'freelancer' => 'Freelancer & Kreator',
            'all' => 'All-in-One',
        ];
        $label = $labels[$persona] ?? ucfirst($persona);

        session()->flash('success', "Profil profesi {$user->name} berhasil diubah ke {$label}.");
    }

    public function setTierDirect(int $userId, string $tier, ?int $days = null)
    {
        $user = User::findOrFail($userId);
        $user->subscription_tier = $tier;

        if ($tier === 'pro') {
            $user->subscription_ends_at = $days ? now()->addDays($days) : now()->addMonth();
        } elseif ($tier === 'lifetime') {
            $user->subscription_ends_at = null;
            $user->trial_ends_at = null;
        } elseif ($tier === 'trial') {
            $user->trial_ends_at = $days ? now()->addDays($days) : now()->addDays(14);
        } elseif ($tier === 'free') {
            $user->subscription_ends_at = null;
            $user->trial_ends_at = null;
        }

        $user->save();
        session()->flash('success', "Paket {$user->name} berhasil diubah ke {$tier}.");
    }

    public function toggleBan(int $userId)
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id() || strtolower($user->email) === 'zakitripamungkas03@gmail.com') {
            session()->flash('error', 'Akun Superadmin Utama tidak dapat dibekukan.');
            return;
        }

        $user->is_banned = !$user->is_banned;
        if ($user->is_banned) {
            $user->banned_reason = 'Dibekukan oleh Administrator pada ' . now()->format('d M Y');
        } else {
            $user->banned_reason = null;
        }
        $user->save();

        $action = $user->is_banned ? 'dibekukan (banned)' : 'diaktifkan kembali';
        session()->flash('success', "Akun {$user->name} berhasil {$action}.");
    }

    public function impersonateUser(int $userId)
    {
        $targetUser = User::findOrFail($userId);
        if ($targetUser->id === auth()->id()) {
            return;
        }

        // Store original admin ID in session
        session(['admin_impersonator_id' => auth()->id()]);
        Auth::login($targetUser);

        return redirect()->route('dashboard')->with('success', "Anda sekarang masuk sebagai {$targetUser->name}.");
    }

    public function render()
    {
        $query = User::withCount(['transactions', 'projects', 'accounts'])->latest();

        if (!empty(trim($this->search))) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . trim($this->search) . '%')
                  ->orWhere('email', 'like', '%' . trim($this->search) . '%');
            });
        }

        if ($this->filterPersona !== 'all') {
            $query->where('financial_persona', $this->filterPersona);
        }

        if ($this->filterTier === 'pro') {
            $query->where('subscription_tier', 'pro')->where(fn($q) => $q->whereNull('subscription_ends_at')->orWhere('subscription_ends_at', '>', now()));
        } elseif ($this->filterTier === 'lifetime') {
            $query->where('subscription_tier', 'lifetime');
        } elseif ($this->filterTier === 'trial') {
            $query->where('subscription_tier', 'trial')->where(fn($q) => $q->whereNull('trial_ends_at')->orWhere('trial_ends_at', '>', now()));
        } elseif ($this->filterTier === 'free') {
            $query->where('subscription_tier', 'free');
        } elseif ($this->filterTier === 'banned') {
            $query->where('is_banned', true);
        } elseif ($this->filterTier === 'admin') {
            $query->where('role', 'admin');
        }

        $users = $query->paginate(12);

        return view('livewire.admin.users.index', [
            'users' => $users,
        ]);
    }
}
