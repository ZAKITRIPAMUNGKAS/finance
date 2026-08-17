<div class="space-y-6">

    <!-- ADMIN HERO BANNER -->
    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl relative overflow-hidden">
        <!-- Glow Accents -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-[#C6F24D]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6F24D]/15 border border-[#C6F24D]/30 text-[#C6F24D] text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-[#C6F24D]" strokeWidth="2.5" />
                    <span>PortoFinance Superadmin Command Center</span>
                </div>
                <h1 class="text-xl sm:text-3xl font-black tracking-tight text-white">
                    Panel Kontrol & Manajemen <span class="text-[#C6F24D]">Pengguna SaaS</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Pantau pertumbuhan pengguna, alokasi paket (Trial, Free, Pro, Lifetime VIP), dan kelola hak akses sistem secara terpusat.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 rounded-2xl bg-[#C6F24D] text-slate-950 hover:bg-[#b8e640] text-xs font-black transition-all shadow-md flex items-center gap-2 cursor-pointer active-tap">
                    <x-icon name="users" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
                    <span>Kelola Data Pengguna</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 6 METRIC STAT CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        
        <!-- Total Users -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono uppercase font-bold text-slate-400">Total Pengguna</span>
                <x-icon name="users" class="w-4 h-4 text-slate-700" />
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-950 font-mono">{{ number_format($totalUsers, 0, ',', '.') }}</div>
            <p class="text-[10px] text-slate-500 font-semibold">User Terdaftar</p>
        </div>

        <!-- Pro Subscribers -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono uppercase font-bold text-emerald-600">PRO Member</span>
                <x-icon name="award" class="w-4 h-4 text-emerald-600" strokeWidth="2.5" />
            </div>
            <div class="text-xl sm:text-2xl font-black text-emerald-950 font-mono">{{ number_format($proUsers, 0, ',', '.') }}</div>
            <p class="text-[10px] text-emerald-700 font-semibold">Langganan Aktif</p>
        </div>

        <!-- Lifetime VIP -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono uppercase font-bold text-purple-600">Lifetime VIP</span>
                <x-icon name="crown" class="w-4 h-4 text-purple-600" strokeWidth="2.5" />
            </div>
            <div class="text-xl sm:text-2xl font-black text-purple-950 font-mono">{{ number_format($lifetimeUsers, 0, ',', '.') }}</div>
            <p class="text-[10px] text-purple-700 font-semibold">Akses Permanen</p>
        </div>

        <!-- Free Trial -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono uppercase font-bold text-amber-600">Free Trial</span>
                <x-icon name="clock" class="w-4 h-4 text-amber-600" />
            </div>
            <div class="text-xl sm:text-2xl font-black text-amber-950 font-mono">{{ number_format($trialUsers, 0, ',', '.') }}</div>
            <p class="text-[10px] text-amber-700 font-semibold">Masa Percobaan</p>
        </div>

        <!-- Free Starter -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono uppercase font-bold text-slate-400">Free Starter</span>
                <x-icon name="user" class="w-4 h-4 text-slate-500" />
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-700 font-mono">{{ number_format($freeUsers, 0, ',', '.') }}</div>
            <p class="text-[10px] text-slate-400 font-semibold">Paket Gratis</p>
        </div>

        <!-- Banned / Suspend -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-mono uppercase font-bold text-rose-500">Nonaktif / Banned</span>
                <x-icon name="ban" class="w-4 h-4 text-rose-500" />
            </div>
            <div class="text-xl sm:text-2xl font-black text-rose-950 font-mono">{{ number_format($bannedUsers, 0, ',', '.') }}</div>
            <p class="text-[10px] text-rose-600 font-semibold">Akun Dibekukan</p>
        </div>

    </div>

    <!-- PLATFORM USAGE & RECENT USERS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT: RECENT USERS (7 COLS) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-950 tracking-tight">Pengguna Baru Terdaftar</h3>
                    <p class="text-xs text-slate-400">8 pendaftar akun terbaru di sistem</p>
                </div>
                <a href="{{ route('admin.users') }}" class="text-xs font-bold text-slate-900 hover:text-[#7cb305] flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            <div class="space-y-2.5 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-mono font-bold uppercase text-slate-400 border-b border-slate-100 pb-2">
                            <th class="py-2">Pengguna</th>
                            <th class="py-2">Paket / Tier</th>
                            <th class="py-2">Role</th>
                            <th class="py-2">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentUsers as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5">
                                <div class="font-extrabold text-slate-950">{{ $user->name }}</div>
                                <div class="text-[11px] font-mono text-slate-400">{{ $user->email }}</div>
                            </td>
                            <td class="py-2.5">
                                @if($user->isLifetime())
                                    <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-900 font-extrabold text-[10px]">👑 Lifetime VIP</span>
                                @elseif($user->subscription_tier === 'pro' && ($user->subscription_ends_at === null || $user->subscription_ends_at->isFuture()))
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-900 font-extrabold text-[10px]">⭐ PRO Member</span>
                                @elseif($user->isTrial())
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-extrabold text-[10px]">🎁 Trial ({{ $user->remaining_trial_days }}h)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">Free Starter</span>
                                @endif
                            </td>
                            <td class="py-2.5">
                                @if($user->isAdmin())
                                    <span class="px-2 py-0.5 rounded-md bg-slate-950 text-[#C6F24D] font-mono font-black text-[10px]">ADMIN</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[10px]">USER</span>
                                @endif
                            </td>
                            <td class="py-2.5 text-slate-500 font-mono text-[11px]">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT: PLATFORM ECOSYSTEM STATS (5 COLS) -->
        <div class="lg:col-span-5 space-y-4">
            
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-extrabold text-slate-950 tracking-tight">Total Volume Ekosistem Finansial</h3>
                
                <div class="p-4 rounded-2xl bg-slate-950 text-white space-y-1">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Transaksi Diproses</span>
                    <div class="text-2xl font-black font-mono text-[#C6F24D]">
                        Rp {{ number_format($totalTransactionsVolume, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-300 font-medium">
                        Dari total {{ number_format($totalTransactionsCount, 0, ',', '.') }} catatan transaksi di sistem.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-0.5">
                        <span class="text-[10px] font-mono uppercase font-bold text-slate-400">Total Project</span>
                        <div class="text-lg font-black text-slate-900 font-mono">{{ number_format($totalProjects, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-0.5">
                        <span class="text-[10px] font-mono uppercase font-bold text-slate-400">Total Invoice</span>
                        <div class="text-lg font-black text-slate-900 font-mono">{{ number_format($totalInvoices, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- QUICK ADMIN ACTIONS -->
            <div class="bg-[#F8F9FA] border border-slate-200/80 rounded-3xl p-5 space-y-3">
                <span class="text-[10px] font-mono font-extrabold uppercase tracking-wider text-slate-400">Pusat Aksi Cepat Superadmin</span>
                
                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('admin.users') }}" class="p-3 rounded-2xl bg-white border border-slate-200 hover:border-slate-400 transition-all flex items-center justify-between text-xs font-bold text-slate-900 group">
                        <div class="flex items-center gap-2.5">
                            <x-icon name="user-check" class="w-4 h-4 text-emerald-600" />
                            <span>Kelola & Upgrade Paket Akun User</span>
                        </div>
                        <x-icon name="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
                    </a>

                    <a href="{{ route('dashboard') }}" class="p-3 rounded-2xl bg-white border border-slate-200 hover:border-slate-400 transition-all flex items-center justify-between text-xs font-bold text-slate-900 group">
                        <div class="flex items-center gap-2.5">
                            <x-icon name="arrow-left" class="w-4 h-4 text-slate-600" />
                            <span>Kembali ke Dashboard Finansial Saya</span>
                        </div>
                        <x-icon name="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform" />
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
