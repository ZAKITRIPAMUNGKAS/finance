<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 flex items-center gap-1">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali ke Dashboard Admin</span>
                </a>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-950 mt-1">
                Data & Hak Akses Pengguna
            </h1>
            <p class="text-xs text-slate-500 font-medium">
                Kontrol paket langganan (Trial, Free, Pro, Lifetime), status akun, dan kelola peran admin.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                Total: {{ $users->total() }} Pengguna
            </span>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
    <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
        <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
        <x-icon name="alert-circle" class="w-4 h-4 text-rose-600 shrink-0" />
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- FILTER BAR & SEARCH -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            
            <!-- Search Input -->
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari nama atau email..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-[#F8F9FA] border border-slate-200 rounded-2xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-950 font-medium transition-all">
                <div class="absolute left-3 top-3 text-slate-400">
                    <x-icon name="search" class="w-4 h-4" />
                </div>
            </div>

            <!-- Tier Filter Pills -->
            <div class="flex items-center gap-1.5 flex-wrap w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
                <button type="button" wire:click="$set('filterTier', 'all')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'all' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </button>
                <button type="button" wire:click="$set('filterTier', 'pro')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'pro' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    ⭐ PRO
                </button>
                <button type="button" wire:click="$set('filterTier', 'lifetime')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'lifetime' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-800 hover:bg-purple-100' }}">
                    👑 Lifetime
                </button>
                <button type="button" wire:click="$set('filterTier', 'trial')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'trial' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    🎁 Trial
                </button>
                <button type="button" wire:click="$set('filterTier', 'free')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'free' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Free
                </button>
                <button type="button" wire:click="$set('filterTier', 'admin')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'admin' ? 'bg-[#C6F24D] text-slate-950' : 'bg-[#C6F24D]/15 text-slate-900 hover:bg-[#C6F24D]/30' }}">
                    🛡️ Admin
                </button>
                <button type="button" wire:click="$set('filterTier', 'banned')" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterTier === 'banned' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                    🚫 Banned
                </button>
            </div>
        </div>

        <!-- USERS TABLE -->
        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-mono font-bold uppercase text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-3">Role</th>
                        <th class="py-3 px-3">Paket / Status</th>
                        <th class="py-3 px-3">Aktivitas Data</th>
                        <th class="py-3 px-3">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $user->is_banned ? 'bg-rose-50/30' : '' }}">
                        
                        <!-- User Name & Email -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black text-xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-950 flex items-center gap-1.5">
                                        <span>{{ $user->name }}</span>
                                        @if($user->is_banned)
                                            <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-mono font-bold">BANNED</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="py-3.5 px-3">
                            @if($user->isAdmin())
                                <span class="px-2 py-0.5 rounded-lg bg-slate-950 text-[#C6F24D] font-mono font-extrabold text-[10px] inline-flex items-center gap-1">
                                    <x-icon name="shield" class="w-2.5 h-2.5 text-[#C6F24D]" />
                                    <span>ADMIN</span>
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 font-mono text-[10px]">
                                    USER
                                </span>
                            @endif
                        </td>

                        <!-- Tier Badge -->
                        <td class="py-3.5 px-3">
                            @if($user->isLifetime())
                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 font-extrabold text-[11px]">
                                    <x-icon name="crown" class="w-3 h-3 text-purple-600" />
                                    <span>Lifetime VIP</span>
                                </div>
                            @elseif($user->subscription_tier === 'pro' && ($user->subscription_ends_at === null || $user->subscription_ends_at->isFuture()))
                                <div class="space-y-0.5">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-extrabold text-[11px]">
                                        <x-icon name="award" class="w-3 h-3 text-emerald-600" />
                                        <span>PRO Member</span>
                                    </div>
                                    @if($user->subscription_ends_at)
                                        <div class="text-[10px] font-mono text-slate-400">s/d {{ $user->subscription_ends_at->format('d M Y') }}</div>
                                    @endif
                                </div>
                            @elseif($user->isTrial())
                                <div class="space-y-0.5">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-[11px]">
                                        <x-icon name="clock" class="w-3 h-3 text-amber-600" />
                                        <span>Trial ({{ $user->remaining_trial_days }} hari)</span>
                                    </div>
                                    @if($user->trial_ends_at)
                                        <div class="text-[10px] font-mono text-slate-400">s/d {{ $user->trial_ends_at->format('d M Y') }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-bold text-[11px]">
                                    <x-icon name="user" class="w-3 h-3 text-slate-500" />
                                    <span>Free Starter</span>
                                </div>
                            @endif
                        </td>

                        <!-- Activity Counts -->
                        <td class="py-3.5 px-3">
                            <div class="flex items-center gap-2 font-mono text-[11px] text-slate-600">
                                <span title="Total Transaksi" class="px-1.5 py-0.5 bg-slate-100 rounded">{{ $user->transactions_count }} Trx</span>
                                <span title="Total Project" class="px-1.5 py-0.5 bg-slate-100 rounded">{{ $user->projects_count }} Proj</span>
                            </div>
                        </td>

                        <!-- Registered Date -->
                        <td class="py-3.5 px-3 font-mono text-slate-500 text-[11px]">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        <!-- Quick Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                
                                <!-- Edit Tier / Status Button -->
                                <button type="button" 
                                        wire:click="openEditModal({{ $user->id }})" 
                                        class="px-2.5 py-1 rounded-xl bg-slate-950 text-white hover:bg-slate-800 text-[11px] font-bold transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                                    <x-icon name="edit-3" class="w-3 h-3" />
                                    <span>Ubah Paket</span>
                                </button>

                                <!-- Impersonate / Login As -->
                                @if($user->id !== auth()->id())
                                <button type="button" 
                                        wire:click="impersonateUser({{ $user->id }})" 
                                        title="Buka akun sebagai pengguna ini untuk bantuan teknis" 
                                        class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer">
                                    <x-icon name="log-in" class="w-3.5 h-3.5" />
                                </button>
                                @endif

                                <!-- Ban / Unban Toggle -->
                                @if($user->id !== auth()->id())
                                <button type="button" 
                                        wire:click="toggleBan({{ $user->id }})" 
                                        title="{{ $user->is_banned ? 'Aktifkan Akun' : 'Bekukan Akun (Ban)' }}" 
                                        class="p-1.5 rounded-xl transition-colors cursor-pointer {{ $user->is_banned ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                    <x-icon name="{{ $user->is_banned ? 'unlock' : 'lock' }}" class="w-3.5 h-3.5" />
                                </button>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada pengguna yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $users->links() }}
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4 animate-in fade-in zoom-in-95 duration-150">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-950">Atur Paket & Hak Akses Akun</h3>
                    <p class="text-[11px] text-slate-400 font-mono">{{ $editName }} ({{ $editEmail }})</p>
                </div>
                <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <form wire:submit.prevent="saveUserChanges" class="space-y-4">
                
                <!-- 1. Subscription Tier -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Tingkatan Paket (Tier)</label>
                    <select wire:model.live="editTier" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950">
                        <option value="trial">🎁 Free Trial (Masa Percobaan)</option>
                        <option value="free">🌱 Free Starter (Paket Gratis Terbatas)</option>
                        <option value="pro">⭐ PRO Member (Langganan Berbayar)</option>
                        <option value="lifetime">👑 Lifetime VIP (Akses Permanen Selamanya)</option>
                    </select>
                </div>

                <!-- 2. Extend Days (If Pro or Trial) -->
                @if(in_array($editTier, ['pro', 'trial']))
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Durasi Masa Aktif</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" wire:click="$set('extendDays', 7)" class="py-1.5 text-xs font-bold rounded-xl border transition-all {{ $extendDays === 7 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200' }}">+7 Hari</button>
                        <button type="button" wire:click="$set('extendDays', 14)" class="py-1.5 text-xs font-bold rounded-xl border transition-all {{ $extendDays === 14 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200' }}">+14 Hari</button>
                        <button type="button" wire:click="$set('extendDays', 30)" class="py-1.5 text-xs font-bold rounded-xl border transition-all {{ $extendDays === 30 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200' }}">+30 Hari</button>
                        <button type="button" wire:click="$set('extendDays', 365)" class="py-1.5 text-xs font-bold rounded-xl border transition-all {{ $extendDays === 365 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200' }}">+1 Tahun</button>
                    </div>
                </div>
                @endif

                <!-- 3. Role Selection -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Peran Sistem (Role)</label>
                    <select wire:model="editRole" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950">
                        <option value="user">User Biasa</option>
                        <option value="admin">Superadministrator (Akses Penuh Panel Admin)</option>
                    </select>
                </div>

                <!-- 4. Ban / Suspend Toggle -->
                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="editIsBanned" class="rounded text-rose-600 focus:ring-rose-500 w-4 h-4">
                        <span class="text-xs font-bold text-rose-700">Bekukan / Nonaktifkan Akun Ini (Ban)</span>
                    </label>

                    @if($editIsBanned)
                    <div>
                        <input type="text" 
                               wire:model="editBannedReason" 
                               placeholder="Alasan pembekuan akun..." 
                               class="w-full p-2 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-900 placeholder-rose-400 focus:outline-none">
                    </div>
                    @endif
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="closeEditModal" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-extrabold transition-all shadow-sm cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
    @endif

</div>
