<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors w-fit mb-1">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Dashboard Admin</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-950">
                Data & Hak Akses Pengguna
            </h1>
            <p class="text-xs text-slate-500 font-medium max-w-xl">
                Kelola tingkatan paket langganan (Trial, Free, Pro, Lifetime VIP), hak akses admin, dan kontrol status pembekuan akun pengguna.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <div class="px-3.5 py-1.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-mono font-bold text-slate-700">
                    Total: <strong class="text-slate-950 font-black">{{ $users->total() }}</strong> Pengguna
                </span>
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-950 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-xs animate-in fade-in duration-200">
        <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
            <x-icon name="check" class="w-4 h-4" strokeWidth="3" />
        </div>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-950 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-xs animate-in fade-in duration-200">
        <div class="w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
            <x-icon name="alert-triangle" class="w-4 h-4" strokeWidth="2.5" />
        </div>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- MAIN CARD CONTAINER -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5">
        
        <!-- SEARCH & FILTER BAR -->
        <div class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center justify-between pb-3 border-b border-slate-100">
            
            <!-- Search Input -->
            <div class="relative w-full lg:w-72">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari nama atau email pengguna..." 
                       class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-950 focus:bg-white font-medium transition-all">
                <div class="absolute left-3 top-3 text-slate-400">
                    <x-icon name="search" class="w-4 h-4" />
                </div>
                @if($search)
                <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-700">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
                @endif
            </div>

            <!-- Tier Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0 scrollbar-none w-full lg:w-auto -mx-1 px-1" style="-webkit-overflow-scrolling: touch;">
                <button type="button" wire:click="$set('filterTier', 'all')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'all' ? 'bg-slate-950 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Paket
                </button>
                <button type="button" wire:click="$set('filterTier', 'pro')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'pro' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    ⭐ PRO
                </button>
                <button type="button" wire:click="$set('filterTier', 'lifetime')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'lifetime' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-800 hover:bg-purple-100' }}">
                    👑 Lifetime
                </button>
                <button type="button" wire:click="$set('filterTier', 'trial')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'trial' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    🎁 Trial
                </button>
                <button type="button" wire:click="$set('filterTier', 'free')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'free' ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Free
                </button>
                <button type="button" wire:click="$set('filterTier', 'admin')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'admin' ? 'bg-[#C6F24D] text-slate-950 shadow-xs' : 'bg-[#C6F24D]/15 text-slate-900 hover:bg-[#C6F24D]/30' }}">
                    🛡️ Admin
                </button>
                <button type="button" wire:click="$set('filterTier', 'banned')" 
                        class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $filterTier === 'banned' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                    🚫 Banned
                </button>
            </div>
        </div>

        <!-- PERSONA FILTER PILLS -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-2 scrollbar-none w-full -mx-1 px-1 text-xs" style="-webkit-overflow-scrolling: touch;">
            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 shrink-0 mr-1">Filter Profesi:</span>
            <button type="button" wire:click="$set('filterPersona', 'all')" 
                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer {{ $filterPersona === 'all' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </button>
            <button type="button" wire:click="$set('filterPersona', 'student')" 
                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer {{ $filterPersona === 'student' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                🎓 Pelajar & Mahasiswa
            </button>
            <button type="button" wire:click="$set('filterPersona', 'employee')" 
                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer {{ $filterPersona === 'employee' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                💼 Karyawan & Kantoran
            </button>
            <button type="button" wire:click="$set('filterPersona', 'merchant')" 
                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer {{ $filterPersona === 'merchant' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                🏪 Pedagang & UMKM
            </button>
            <button type="button" wire:click="$set('filterPersona', 'freelancer')" 
                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer {{ $filterPersona === 'freelancer' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-800 hover:bg-purple-100' }}">
                ⚡ Freelancer & Kreator
            </button>
            <button type="button" wire:click="$set('filterPersona', 'all_in_one')" 
                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all shrink-0 cursor-pointer {{ $filterPersona === 'all_in_one' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                🌟 All-in-One
            </button>
        </div>

        <!-- DESKTOP TABLE VIEW (md:block) -->
        <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200/70">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-mono font-bold uppercase text-slate-500 border-b border-slate-200/70">
                        <th class="py-3.5 px-4 font-black">Pengguna</th>
                        <th class="py-3.5 px-3 font-black">Profesi</th>
                        <th class="py-3.5 px-3 font-black">Role</th>
                        <th class="py-3.5 px-3 font-black">Paket / Status</th>
                        <th class="py-3.5 px-3 font-black">Aktivitas Data</th>
                        <th class="py-3.5 px-3 font-black">Terdaftar</th>
                        <th class="py-3.5 px-4 text-right font-black">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $user->is_banned ? 'bg-rose-50/30' : '' }}">
                        
                        <!-- User Profile -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-[#C6F24D] border border-slate-700 flex items-center justify-center font-mono font-black text-xs shrink-0 shadow-xs">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-950 flex items-center gap-1.5 truncate">
                                        <span>{{ $user->name }}</span>
                                        @if($user->is_banned)
                                            <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 text-[9px] font-mono font-black">BANNED</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-400 truncate">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Persona / Profesi -->
                        <td class="py-3.5 px-3">
                            @if($user->isStudent())
                                <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-extrabold text-[11px] inline-flex items-center gap-1 border border-emerald-200">
                                    <x-icon name="graduation-cap" class="w-3 h-3 text-emerald-700" />
                                    <span>Pelajar</span>
                                </span>
                            @elseif($user->isEmployee())
                                <span class="px-2.5 py-1 rounded-xl bg-blue-50 text-blue-800 font-extrabold text-[11px] inline-flex items-center gap-1 border border-blue-200">
                                    <x-icon name="briefcase" class="w-3 h-3 text-blue-700" />
                                    <span>Karyawan</span>
                                </span>
                            @elseif($user->isMerchant())
                                <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 font-extrabold text-[11px] inline-flex items-center gap-1 border border-amber-200">
                                    <x-icon name="shopping-bag" class="w-3 h-3 text-amber-700" />
                                    <span>Pedagang</span>
                                </span>
                            @elseif($user->financial_persona === 'all')
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-extrabold text-[11px] inline-flex items-center gap-1 border border-slate-200">
                                    <x-icon name="star" class="w-3 h-3 text-amber-500" />
                                    <span>All-in-One</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-xl bg-purple-50 text-purple-800 font-extrabold text-[11px] inline-flex items-center gap-1 border border-purple-200">
                                    <x-icon name="laptop" class="w-3 h-3 text-purple-700" />
                                    <span>Freelancer</span>
                                </span>
                            @endif
                        </td>

                        <!-- Role -->
                        <td class="py-3.5 px-3">
                            @if($user->isAdmin())
                                <span class="px-2.5 py-1 rounded-xl bg-slate-950 text-[#C6F24D] font-mono font-black text-[10px] inline-flex items-center gap-1 shadow-xs border border-slate-800">
                                    <x-icon name="shield" class="w-3 h-3 text-[#C6F24D]" />
                                    <span>ADMIN</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-mono font-bold text-[10px]">
                                    USER
                                </span>
                            @endif
                        </td>

                        <!-- Tier Badge -->
                        <td class="py-3.5 px-3">
                            @if($user->isLifetime())
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-purple-50 border border-purple-200/80 text-purple-900 font-extrabold text-xs">
                                    <x-icon name="crown" class="w-3.5 h-3.5 text-purple-600" />
                                    <span>Lifetime VIP</span>
                                </div>
                            @elseif($user->subscription_tier === 'pro' && ($user->subscription_ends_at === null || $user->subscription_ends_at->isFuture()))
                                <div class="space-y-0.5">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 font-extrabold text-xs">
                                        <x-icon name="award" class="w-3.5 h-3.5 text-emerald-600" />
                                        <span>PRO Member</span>
                                    </div>
                                    @if($user->subscription_ends_at)
                                        <div class="text-[10px] font-mono text-slate-400">s/d {{ $user->subscription_ends_at->format('d M Y') }}</div>
                                    @endif
                                </div>
                            @elseif($user->isTrial())
                                <div class="space-y-0.5">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 font-extrabold text-xs">
                                        <x-icon name="clock" class="w-3.5 h-3.5 text-amber-600" />
                                        <span>Trial ({{ $user->remaining_trial_days }} hari)</span>
                                    </div>
                                    @if($user->trial_ends_at)
                                        <div class="text-[10px] font-mono text-slate-400">s/d {{ $user->trial_ends_at->format('d M Y') }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">
                                    <x-icon name="user" class="w-3.5 h-3.5 text-slate-400" />
                                    <span>Free Starter</span>
                                </div>
                            @endif
                        </td>

                        <!-- Activity Data -->
                        <td class="py-3.5 px-3">
                            <div class="flex items-center gap-1.5 font-mono text-[11px]">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-lg font-bold border border-slate-200/50" title="Jumlah Transaksi">
                                    {{ $user->transactions_count }} Trx
                                </span>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-lg font-bold border border-slate-200/50" title="Jumlah Proyek">
                                    {{ $user->projects_count }} Proj
                                </span>
                            </div>
                        </td>

                        <!-- Registered -->
                        <td class="py-3.5 px-3 font-mono text-slate-500 text-[11px]">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                
                                <!-- Ubah Paket -->
                                <button type="button" 
                                        wire:click="openEditModal({{ $user->id }})" 
                                        class="px-3 py-1.5 rounded-xl bg-slate-950 text-white hover:bg-slate-800 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active-tap">
                                    <x-icon name="edit-3" class="w-3.5 h-3.5" />
                                    <span>Ubah Paket & Role</span>
                                </button>

                                <!-- Impersonate / Login As -->
                                @if($user->id !== auth()->id())
                                <button type="button" 
                                        wire:click="impersonateUser({{ $user->id }})" 
                                        title="Buka akun sebagai pengguna ini" 
                                        class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer active-tap">
                                    <x-icon name="log-in" class="w-4 h-4" />
                                </button>
                                @endif

                                <!-- Ban Toggle -->
                                @if($user->id !== auth()->id() && strtolower($user->email) !== 'zakitripamungkas03@gmail.com')
                                <button type="button" 
                                        wire:click="toggleBan({{ $user->id }})" 
                                        title="{{ $user->is_banned ? 'Buka Blokir (Unban)' : 'Bekukan Akun (Ban)' }}" 
                                        class="p-1.5 rounded-xl transition-colors cursor-pointer active-tap {{ $user->is_banned ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                    <x-icon name="{{ $user->is_banned ? 'unlock' : 'lock' }}" class="w-4 h-4" />
                                </button>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 text-xs font-medium">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                <x-icon name="search" class="w-6 h-6" />
                            </div>
                            Tidak ada pengguna yang cocok dengan filter atau pencarian Anda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARD VIEW (block md:hidden) -->
        <div class="block md:hidden space-y-3">
            @forelse($users as $user)
            <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/70 space-y-3 shadow-xs {{ $user->is_banned ? 'bg-rose-50/40 border-rose-200' : '' }}">
                
                <!-- Card Top: Avatar, Name & Email -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-[#C6F24D] border border-slate-700 flex items-center justify-center font-mono font-black text-xs shrink-0 shadow-xs">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-slate-950 text-xs sm:text-sm truncate">{{ $user->name }}</div>
                        <div class="text-[11px] font-mono text-slate-400 truncate">{{ $user->email }}</div>
                    </div>
                </div>

                <!-- Card Badges: Role, Persona, Tier, Status (Clean Row) -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    @if($user->isAdmin())
                        <span class="px-2 py-0.5 rounded-lg bg-slate-950 text-[#C6F24D] text-[10px] font-mono font-black border border-slate-800 inline-flex items-center gap-1">
                            <x-icon name="shield" class="w-2.5 h-2.5 text-[#C6F24D]" />
                            <span>ADMIN</span>
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-lg bg-slate-200 text-slate-700 text-[10px] font-mono font-bold">USER</span>
                    @endif

                    <!-- Persona Badge Mobile -->
                    @if($user->isStudent())
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-bold">🎓 Pelajar</span>
                    @elseif($user->isEmployee())
                        <span class="px-2 py-0.5 rounded-lg bg-blue-100 text-blue-800 text-[10px] font-bold">💼 Karyawan</span>
                    @elseif($user->isMerchant())
                        <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-800 text-[10px] font-bold">🏪 Pedagang</span>
                    @elseif($user->financial_persona === 'all')
                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 text-[10px] font-bold">🌟 All-in-One</span>
                    @else
                        <span class="px-2 py-0.5 rounded-lg bg-purple-100 text-purple-800 text-[10px] font-bold">⚡ Freelancer</span>
                    @endif

                    @if($user->isLifetime())
                        <span class="px-2.5 py-0.5 rounded-lg bg-purple-100 text-purple-900 font-extrabold text-[11px] inline-flex items-center gap-1">
                            <x-icon name="crown" class="w-3 h-3 text-purple-700" />
                            <span>Lifetime</span>
                        </span>
                    @elseif($user->subscription_tier === 'pro' && ($user->subscription_ends_at === null || $user->subscription_ends_at->isFuture()))
                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-900 font-extrabold text-[11px] inline-flex items-center gap-1">
                            <x-icon name="award" class="w-3 h-3 text-emerald-700" />
                            <span>PRO</span>
                        </span>
                    @elseif($user->isTrial())
                        <span class="px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-900 font-extrabold text-[11px]">
                            Trial ({{ $user->remaining_trial_days }}h)
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-200 text-slate-700 text-[10px] font-mono font-bold">Free</span>
                    @endif

                    @if($user->is_banned)
                        <span class="px-2 py-0.5 rounded-lg bg-rose-600 text-white text-[10px] font-mono font-black">BANNED</span>
                    @endif
                </div>

                <!-- Card Middle: Activity Stats & Date -->
                <div class="flex items-center justify-between text-[11px] font-mono text-slate-500 pt-2 border-t border-slate-200/60">
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 bg-white rounded-lg border border-slate-200 font-bold">{{ $user->transactions_count }} Trx</span>
                        <span class="px-2 py-0.5 bg-white rounded-lg border border-slate-200 font-bold">{{ $user->projects_count }} Proj</span>
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ $user->created_at->format('d M Y') }}
                    </div>
                </div>

                <!-- Card Bottom: Action Buttons -->
                <div class="flex items-center gap-2 pt-1">
                    <button type="button" 
                            wire:click="openEditModal({{ $user->id }})" 
                            class="flex-1 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-black flex items-center justify-center gap-1.5 cursor-pointer active-tap shadow-xs">
                        <x-icon name="edit-3" class="w-3.5 h-3.5" />
                        <span>Ubah Paket & Role</span>
                    </button>

                    @if($user->id !== auth()->id())
                    <button type="button" 
                            wire:click="impersonateUser({{ $user->id }})" 
                            title="Buka akun sebagai pengguna ini" 
                            class="p-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 cursor-pointer active-tap">
                        <x-icon name="log-in" class="w-4 h-4" />
                    </button>
                    @endif

                    @if($user->id !== auth()->id() && strtolower($user->email) !== 'zakitripamungkas03@gmail.com')
                    <button type="button" 
                            wire:click="toggleBan({{ $user->id }})" 
                            title="{{ $user->is_banned ? 'Buka Blokir' : 'Bekukan Akun' }}" 
                            class="p-2.5 rounded-xl cursor-pointer active-tap {{ $user->is_banned ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-50 text-rose-700' }}">
                        <x-icon name="{{ $user->is_banned ? 'unlock' : 'lock' }}" class="w-4 h-4" />
                    </button>
                    @endif
                </div>

            </div>
            @empty
            <div class="py-8 text-center text-slate-400 text-xs font-medium">
                Tidak ada pengguna yang cocok dengan filter atau pencarian Anda.
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $users->links() }}
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4 animate-in fade-in zoom-in-95 duration-150">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-950">Atur Paket & Hak Akses Akun</h3>
                    <p class="text-[11px] text-slate-500 font-mono">{{ $editName }} ({{ $editEmail }})</p>
                </div>
                <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <form wire:submit.prevent="saveUserChanges" class="space-y-4">
                
                <!-- 1. Subscription Tier -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Tingkatan Paket (Tier)</label>
                    <select wire:model.live="editTier" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950 cursor-pointer">
                        <option value="trial">🎁 Free Trial (Masa Percobaan)</option>
                        <option value="free">🌱 Free Starter (Paket Gratis Terbatas)</option>
                        <option value="pro">⭐ PRO Member (Langganan Berbayar)</option>
                        <option value="lifetime">👑 Lifetime VIP (Akses Permanen Selamanya)</option>
                    </select>
                </div>

                <!-- 2. Financial Persona Selection -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Profil & Alat Profesi (Financial Persona)</label>
                    <select wire:model="editPersona" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950 cursor-pointer">
                        <option value="student">🎓 Pelajar & Mahasiswa (Batas Jajan Harian & Split Bill)</option>
                        <option value="employee">💼 Karyawan & Kantoran (Alokasi 50/30/20 & Langganan)</option>
                        <option value="merchant">🏪 Pedagang & UMKM (Kas Toko & Kalkulator Margin)</option>
                        <option value="freelancer">⚡ Freelancer & Kreator (Proyek, DP & Invoice WA)</option>
                        <option value="all">🌟 All-in-One (Semua Fitur Aktif)</option>
                    </select>
                </div>

                <!-- 3. Extend Days (If Pro or Trial) -->
                @if(in_array($editTier, ['pro', 'trial']))
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Durasi Masa Aktif</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" wire:click="$set('extendDays', 7)" class="py-2 text-xs font-bold rounded-xl border transition-all cursor-pointer {{ $extendDays === 7 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">+7 Hari</button>
                        <button type="button" wire:click="$set('extendDays', 14)" class="py-2 text-xs font-bold rounded-xl border transition-all cursor-pointer {{ $extendDays === 14 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">+14 Hari</button>
                        <button type="button" wire:click="$set('extendDays', 30)" class="py-2 text-xs font-bold rounded-xl border transition-all cursor-pointer {{ $extendDays === 30 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">+30 Hari</button>
                        <button type="button" wire:click="$set('extendDays', 365)" class="py-2 text-xs font-bold rounded-xl border transition-all cursor-pointer {{ $extendDays === 365 ? 'bg-slate-950 text-white border-slate-950' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">+1 Tahun</button>
                    </div>
                </div>
                @endif

                <!-- 4. Role Selection -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Hak Akses Sistem (System Role)</label>
                    <select wire:model="editRole" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950 cursor-pointer">
                        <option value="user">User Biasa</option>
                        <option value="admin">Superadministrator (Akses Penuh Panel Admin)</option>
                    </select>
                </div>

                <!-- 5. Ban / Suspend Toggle -->
                @if(strtolower($editEmail) !== 'zakitripamungkas03@gmail.com')
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
                                class="w-full p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-900 placeholder-rose-400 focus:outline-none">
                    </div>
                    @endif
                </div>
                @endif

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="closeEditModal" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-black transition-all shadow-sm cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
    @endif

</div>
