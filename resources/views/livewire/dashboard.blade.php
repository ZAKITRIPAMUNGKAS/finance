<div class="space-y-6" x-data="{ isReady: false }" x-init="setTimeout(() => isReady = true, 260)">

    <!-- SKELETON LOADER STATE (SHOWN FIRST ON LOAD) -->
    <div x-show="!isReady" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <x-dashboard-skeleton />
    </div>

    <!-- ACTUAL DASHBOARD CONTENT (REVEALED SMOOTHLY) -->
    <div x-show="isReady" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         class="space-y-6"
         x-cloak>

    <!-- HERO SECTION: CARD & QUICK ACTIONS (Clean Modern FinTech Style) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- HERO DEBIT/BALANCE CARD (7 COLS) -->
        <div class="lg:col-span-7 relative overflow-hidden bg-gradient-to-br from-[#121826] via-[#1E293B] to-[#0F172A] rounded-3xl p-6 sm:p-7 text-white shadow-xl flex flex-col justify-between min-h-[220px]">
            <!-- Background Glow Accent -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-[#C6F24D]/15 blur-3xl pointer-events-none"></div>
            
            <!-- Card Top: Branding & Contactless Wave -->
            <div class="flex items-center justify-between relative z-10">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black tracking-widest text-white/90">FINANCE PRO</span>
                </div>
                
                <div class="flex items-center gap-2 text-white/60">
                    <x-icon name="wifi" class="w-5 h-5 rotate-90" />
                </div>
            </div>

            <!-- Card Center: Available Money in Big Clean Typography -->
            <div class="my-4 relative z-10">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-400 block mb-1">Available Money (Uang Bebas)</span>
                <div class="flex items-baseline gap-2">
                    <h2 class="text-3xl sm:text-4xl font-extrabold font-mono tracking-tight text-white">
                        Rp {{ number_format($availableMoney, 0, ',', '.') }}
                    </h2>
                </div>
                
                <!-- Wishlist Locked Saving Pill Badge -->
                <a href="{{ route('wishlists') }}" class="mt-3 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 hover:bg-white/15 backdrop-blur-md text-[11px] font-semibold text-slate-200 border border-white/10 transition-colors">
                    <x-icon name="lock" class="w-3.5 h-3.5 text-[#C6F24D]" />
                    <span>Rp {{ number_format($wishlistLocked, 0, ',', '.') }} dialokasikan di Wishlist</span>
                    <x-icon name="arrow-right" class="w-3.5 h-3.5 text-slate-400" />
                </a>
            </div>

            <!-- Card Bottom: Metadata & Total Gross Balance -->
            <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-300 relative z-10">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Pemilik Akun</span>
                    <span class="font-bold text-white tracking-wide uppercase">{{ auth()->user()->name ?? 'ZAKI FREELANCE' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Saldo Likuid</span>
                    <span class="font-bold font-mono text-white">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- RIGHT: 4 CIRCULAR ACTIONS & QUICK STATS (5 COLS) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/70 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-5">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Aksi Cepat Finansial</span>
                    <span class="text-[10px] font-mono font-bold text-slate-400">1-Click Fast Action</span>
                </div>
                
                <!-- 5 Quick Action Buttons aligned with System Ecosystem -->
                <div class="grid grid-cols-5 gap-1.5 sm:gap-2.5 text-center">
                    
                    <!-- 1. Income (Featured) -->
                    <button wire:click="$dispatch('open-quick-income')" class="flex flex-col items-center gap-1.5 group cursor-pointer active-tap">
                        <div class="w-11 sm:w-12 h-11 sm:h-12 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] text-slate-950 flex items-center justify-center transition-all shadow-xs group-hover:scale-105">
                            <x-icon name="arrow-down-left" class="w-5 h-5 text-slate-950" strokeWidth="2.5" />
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-950 group-hover:text-black truncate w-full">Income</span>
                    </button>

                    <!-- 2. Expense -->
                    <button wire:click="$dispatch('open-quick-expense')" class="flex flex-col items-center gap-1.5 group cursor-pointer active-tap">
                        <div class="w-11 sm:w-12 h-11 sm:h-12 rounded-2xl bg-slate-100 group-hover:bg-rose-50 text-slate-700 group-hover:text-rose-600 flex items-center justify-center transition-all shadow-2xs group-hover:scale-105">
                            <x-icon name="arrow-up-right" class="w-5 h-5" strokeWidth="2.2" />
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 group-hover:text-rose-600 truncate w-full">Expense</span>
                    </button>

                    <!-- 3. Transfer -->
                    <button wire:click="$dispatch('open-quick-transfer')" class="flex flex-col items-center gap-1.5 group cursor-pointer active-tap">
                        <div class="w-11 sm:w-12 h-11 sm:h-12 rounded-2xl bg-slate-100 group-hover:bg-blue-50 text-slate-700 group-hover:text-blue-600 flex items-center justify-center transition-all shadow-2xs group-hover:scale-105">
                            <x-icon name="arrow-right-left" class="w-5 h-5" strokeWidth="2.2" />
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 group-hover:text-blue-600 truncate w-full">Transfer</span>
                    </button>

                    <!-- 4. Voice AI -->
                    <button wire:click="$dispatch('open-quick-voice')" class="flex flex-col items-center gap-1.5 group cursor-pointer active-tap">
                        <div class="w-11 sm:w-12 h-11 sm:h-12 rounded-2xl bg-rose-50 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-all shadow-2xs group-hover:scale-105">
                            <x-icon name="mic" class="w-5 h-5 text-rose-600" strokeWidth="2.2" />
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 group-hover:text-rose-600 truncate w-full">Voice AI</span>
                    </button>

                    <!-- 5. AI Copilot (Coming Soon) -->
                    <a href="{{ route('ai-copilot') }}" class="flex flex-col items-center gap-1.5 group cursor-pointer active-tap relative">
                        <div class="w-11 sm:w-12 h-11 sm:h-12 rounded-2xl bg-slate-950 group-hover:bg-slate-800 text-[#C6F24D] flex items-center justify-center transition-all shadow-xs group-hover:scale-105 relative">
                            <x-icon name="sparkles" class="w-5 h-5 text-[#C6F24D]" strokeWidth="2.2" />
                            <span class="absolute -top-1.5 -right-1.5 px-1 py-0.2 rounded-full bg-amber-400 text-slate-950 font-black text-[8px] uppercase tracking-tighter">Soon</span>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-900 group-hover:text-slate-950 truncate w-full">AI Copilot</span>
                    </a>

                </div>
            </div>

            <!-- Health & Emergency Mini-Bar -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-[#C6F24D] flex items-center justify-center shadow-2xs">
                        <x-icon name="activity" class="w-4 h-4" strokeWidth="2.5" />
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 block leading-tight">Health Score {{ $healthScore['total_score'] }}/100</span>
                        <span class="text-[10px] text-slate-500 font-medium">{{ $healthScore['status'] }}</span>
                    </div>
                </div>
                <div class="text-right font-mono">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Dana Darurat</span>
                    <span class="font-bold text-slate-900">{{ $emergencyMonths }} Bulan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ADAPTIVE FINANCIAL PERSONA INTELLIGENCE BANNER             -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    @if($user?->isStudent())
    <div class="bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent border border-emerald-500/20 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center font-bold shrink-0 shadow-sm">
                <x-icon name="graduation-cap" class="w-6 h-6 text-slate-950" strokeWidth="2.5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-mono font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Mode Pelajar & Mahasiswa
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Sisa {{ $remainingDays }} hari bulan ini</span>
                </div>
                <div class="mt-1 flex items-baseline gap-2 flex-wrap">
                    <span class="text-xs text-slate-600 font-bold">Batas Aman Jajan Hari Ini:</span>
                    <span class="text-lg sm:text-xl font-black font-mono text-emerald-950">
                        Rp {{ number_format($safeDailySpend, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">/ hari</span>
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    @if($safeDailySpend >= 35000)
                        🟢 Keuangan aman! Uang saku cukup untuk kebutuhan harian & jajan santai.
                    @elseif($safeDailySpend >= 15000)
                        🟡 Waspada boncos. Batasi jajan kopi & prioritaskan makan pokok.
                    @else
                        🔴 Mode hemat ketat! Prioritaskan kebutuhan pokok & uang kos.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" wire:click="$dispatch('open-split-bill')" 
                class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-black flex items-center justify-center gap-2 transition-all shadow-sm cursor-pointer active-tap">
                <x-icon name="users" class="w-4 h-4 text-[#C6F24D]" />
                <span>Bagi Tagihan (Split Bill)</span>
            </button>
        </div>
    </div>
    @elseif($user?->isEmployee())
    <div class="bg-gradient-to-r from-blue-500/10 via-blue-500/5 to-transparent border border-blue-500/20 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center font-bold shrink-0 shadow-sm">
                <x-icon name="briefcase" class="w-6 h-6 text-white" strokeWidth="2.5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-mono font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                        Mode Karyawan & Kantoran
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Alokasi Gaji 50/30/20</span>
                </div>
                <div class="mt-1 flex flex-wrap items-center gap-3 text-xs font-bold">
                    <span class="text-slate-700">🏠 Kebutuhan: <strong class="text-slate-950 font-mono">{{ $needsPct }}%</strong> (target 50%)</span>
                    <span class="text-slate-700">☕ Lifestyle: <strong class="text-slate-950 font-mono">{{ $lifestylePct }}%</strong> (target 30%)</span>
                    <span class="text-slate-700">🎯 Tabungan: <strong class="text-slate-950 font-mono">{{ $savingsPct }}%</strong> (target 20%)</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Pantau proporsi pengeluaran agar gaji bulanan terkelola maksimal.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('subscriptions') }}" 
                class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-black flex items-center justify-center gap-2 transition-all shadow-sm cursor-pointer active-tap">
                <x-icon name="repeat" class="w-4 h-4 text-[#C6F24D]" />
                <span>Audit Langganan Rutin</span>
            </a>
        </div>
    </div>
    @elseif($user?->isMerchant())
    <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20 rounded-3xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold shrink-0 shadow-sm">
                <x-icon name="shopping-bag" class="w-6 h-6 text-slate-950" strokeWidth="2.5" />
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-mono font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        Mode Pedagang & UMKM
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Laba Toko Bulan Ini</span>
                </div>
                <div class="mt-1 flex items-baseline gap-2 flex-wrap">
                    <span class="text-xs text-slate-600 font-bold">Estimasi Laba Bersih:</span>
                    <span class="text-lg sm:text-xl font-black font-mono {{ $merchantProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                        Rp {{ number_format($merchantProfit, 0, ',', '.') }}
                    </span>
                    <span class="text-xs font-bold text-amber-800 font-mono">({{ $merchantMarginPct }}% margin)</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Omset: Rp {{ number_format($merchantSales, 0, ',', '.') }} | HPP & Biaya Ops: Rp {{ number_format($merchantCost, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" wire:click="$dispatch('open-merchant-calculator')" 
                class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-black flex items-center justify-center gap-2 transition-all shadow-sm cursor-pointer active-tap">
                <x-icon name="calculator" class="w-4 h-4 text-[#C6F24D]" />
                <span>Kalkulator Margin Harga</span>
            </button>
        </div>
    </div>
    @endif

    <!-- MAIN GRID 2-COL: CASHFLOW CHART & WALLETS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- CASHFLOW CURVED SPLINE / BAR CHART (7 COLS) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/70 rounded-3xl p-6 shadow-sm flex flex-col justify-between"
             x-data="cashflowChartComponent(@js($chartLabels), @js($incomeData), @js($expenseData))"
             x-init="initChart()"
             wire:ignore>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Statistik Cashflow</h3>
                        <!-- Chart Type Switcher -->
                        <div class="inline-flex p-0.5 rounded-lg bg-slate-100 border border-slate-200/70 text-[10px] font-bold">
                            <button type="button" 
                                    @click="setChartType('line')" 
                                    :class="chartType === 'line' ? 'bg-white text-slate-950 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-900'"
                                    class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                                Kurva (Chart)
                            </button>
                            <button type="button" 
                                    @click="setChartType('bar')" 
                                    :class="chartType === 'bar' ? 'bg-white text-slate-950 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-900'"
                                    class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                                Batang
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Pemasukan vs Pengeluaran 6 Bulan Terakhir</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#EBFAD2] text-slate-900 border border-[#D4F66C] whitespace-nowrap shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#84CC16]"></span>
                        <span>In: Rp {{ number_format($monthlyIncome/1000000, 1) }}jt</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 whitespace-nowrap shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-900"></span>
                        <span>Out: Rp {{ number_format($monthlyExpense/1000000, 1) }}jt</span>
                    </span>
                </div>
            </div>
            <div class="h-60 sm:h-64 w-full relative">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- REKENING & WALLET LIST (5 COLS) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/70 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Dompet & Rekening</h3>
                    <p class="text-xs text-slate-400">Total: Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('accounts') }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 underline">Lihat Semua &rarr;</a>
            </div>

            <div class="space-y-2.5 flex-1 overflow-y-auto max-h-64 pr-1">
                @foreach($accounts as $acc)
                <div class="p-3 bg-[#F8F9FA] rounded-2xl border border-slate-100 flex items-center justify-between hover:bg-slate-100/80 transition-colors">
                    <div class="flex items-center gap-3">
                        <x-account-logo :name="$acc->name" :type="$acc->type" class="w-10 h-10 rounded-2xl" />
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">{{ $acc->name }}</span>
                            <span class="text-[10px] text-slate-400 capitalize">{{ $acc->type }} {{ $acc->account_number ? '• ' . $acc->account_number : '' }}</span>
                        </div>
                    </div>
                    <div class="text-right font-mono">
                        <span class="text-xs font-bold text-slate-900 block">Rp {{ number_format($acc->current_balance, 0, ',', '.') }}</span>
                        <span class="text-[9px] text-slate-400">{{ round(($acc->current_balance / max(1, $totalBalance)) * 100) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PURCHASE WISHLIST MODULE (PRD v1.1 SPOTLIGHT) -->
    <div class="bg-white border border-slate-200/70 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Purchase Wishlist & Saving Progress</h3>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#C6F24D] text-slate-950 uppercase">v1.1</span>
                </div>
                <p class="text-xs text-slate-400">Target menabung barang impian & alat kerja</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('purchase-planning') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <x-icon name="calculator" class="w-3.5 h-3.5" />
                    <span>Simulasi "Can I Afford This?"</span>
                </a>
                <a href="{{ route('wishlists') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                    + Wishlist
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($activeWishlists as $wishlist)
            <div class="bg-[#F8F9FA] border border-slate-200/80 rounded-2xl p-4 flex flex-col justify-between hover:border-slate-300 transition-all group">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full {{ $wishlist->priority === 'critical' ? 'bg-rose-100 text-rose-700' : ($wishlist->priority === 'high' ? 'bg-orange-100 text-orange-700' : 'bg-slate-200 text-slate-700') }}">
                            {{ ucfirst($wishlist->priority) }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-medium truncate">{{ $wishlist->category }}</span>
                    </div>

                    <!-- Item Title -->
                    <h4 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                        {{ $wishlist->name }}
                    </h4>

                    <!-- Price -->
                    <div class="mt-2 flex items-baseline justify-between text-xs font-mono">
                        <span class="text-slate-500">Harga:</span>
                        <span class="font-bold text-slate-950">Rp {{ number_format($wishlist->current_price, 0, ',', '.') }}</span>
                    </div>

                    <!-- Progress Bar (Fresh Lime / Emerald Gradient) -->
                    <div class="mt-2.5">
                        <div class="flex items-center justify-between text-[10px] mb-1 font-mono font-bold">
                            <span class="text-slate-600">Rp {{ number_format($wishlist->saved_amount, 0, ',', '.') }}</span>
                            <span class="text-emerald-700 font-black">{{ $wishlist->progress_percentage }}%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                            <div class="h-full bg-gradient-to-r from-emerald-500 via-lime-500 to-[#C6F24D] rounded-full transition-all" style="width: {{ $wishlist->progress_percentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer Feasibility Badge -->
                <div class="mt-3 pt-2.5 border-t border-slate-200/80 flex items-center justify-between text-[10px]">
                    <span class="text-slate-500">{{ $wishlist->target_date ? \Carbon\Carbon::parse($wishlist->target_date)->translatedFormat('M Y') : '-' }}</span>
                    @if(isset($wishlist->plan_eval))
                        <span class="font-bold px-2 py-0.5 rounded-full {{ $wishlist->plan_eval['status'] === 'realistic' ? 'bg-emerald-100 text-emerald-800' : ($wishlist->plan_eval['status'] === 'at_risk' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $wishlist->plan_eval['label'] }}
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full py-6 text-center text-xs text-slate-400">
                Belum ada item wishlist aktif. <a href="{{ route('wishlists') }}" class="text-slate-900 font-bold underline">Tambah sekarang &rarr;</a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- FREELANCE BUSINESS & RECENT TRANSACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- ACTIVE PROJECTS (7 COLS) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/70 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Project Freelance Aktif</h3>
                    <p class="text-xs text-slate-400">Revenue, biaya operasional & profit margin</p>
                </div>
                <a href="{{ route('projects') }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 underline">Semua &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($activeProjects as $proj)
                <div class="p-3.5 bg-[#F8F9FA] rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900">{{ $proj->name }}</h4>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold {{ $proj->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $proj->status)) }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500">Klien: {{ $proj->client->name ?? '-' }}</span>
                    </div>

                    <div class="flex items-center gap-3 font-mono text-right shrink-0">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Revenue</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900">Rp {{ number_format($proj->total_revenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-l border-slate-200 pl-3">
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Margin</span>
                            <span class="text-xs sm:text-sm font-black text-emerald-600">{{ $proj->margin_percentage }}%</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-xs text-slate-400">Belum ada project aktif saat ini.</div>
                @endforelse
            </div>
        </div>

        <!-- RECENT ACTIVITY (5 COLS - Matching user reference image) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/70 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Recent Activity</h3>
                    <p class="text-xs text-slate-400">Histori transaksi keluar masuk</p>
                </div>
                <a href="{{ route('transactions') }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 underline">View all &rarr;</a>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto max-h-80 pr-1">
                @forelse($recentTransactions as $tx)
                <div class="p-2.5 flex items-center justify-between gap-3 hover:bg-slate-50 rounded-2xl transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Round Avatar / Icon -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $tx->type === 'income' ? 'bg-[#C6F24D] text-slate-950' : ($tx->type === 'expense' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800') }}">
                            @if($tx->type === 'income')
                                <x-icon name="arrow-down-left" class="w-4 h-4" strokeWidth="2.5" />
                            @elseif($tx->type === 'expense')
                                <x-icon name="arrow-up-right" class="w-4 h-4" />
                            @else
                                <x-icon name="arrow-right-left" class="w-4 h-4" />
                            @endif
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block truncate">{{ $tx->description }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }} • {{ $tx->account->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0 font-mono">
                        <span class="text-xs font-extrabold block {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-slate-950' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                        <span class="text-[9px] uppercase font-bold text-slate-400">{{ ucfirst($tx->type) }}</span>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-xs text-slate-400">Belum ada transaksi tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function cashflowChartComponent(labels, income, expense) {
            return {
                labels: labels,
                income: income,
                expense: expense,
                chartType: 'line',

                initChart() {
                    this.$nextTick(() => {
                        this.renderChart();
                    });

                    // Re-render when Livewire updates data
                    if (window.Livewire) {
                        Livewire.hook('commit', ({ component, succeed }) => {
                            succeed(() => {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
                            });
                        });
                    }
                },

                setChartType(type) {
                    if (this.chartType === type) return;
                    this.chartType = type;
                    this.$nextTick(() => {
                        this.renderChart();
                    });
                },

                renderChart() {
                    const canvas = this.$refs.canvas;
                    if (!canvas || typeof Chart === 'undefined') return;

                    // Safely destroy existing non-reactive instance on canvas
                    if (canvas._chart) {
                        canvas._chart.destroy();
                        canvas._chart = null;
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    const isLine = this.chartType === 'line';

                    // Smooth Gradient fills
                    const incomeGradient = ctx.createLinearGradient(0, 0, 0, 240);
                    incomeGradient.addColorStop(0, 'rgba(198, 242, 77, 0.45)');
                    incomeGradient.addColorStop(1, 'rgba(198, 242, 77, 0.01)');

                    const expenseGradient = ctx.createLinearGradient(0, 0, 0, 240);
                    expenseGradient.addColorStop(0, 'rgba(15, 23, 42, 0.15)');
                    expenseGradient.addColorStop(1, 'rgba(15, 23, 42, 0.01)');

                    canvas._chart = new Chart(ctx, {
                        type: isLine ? 'line' : 'bar',
                        data: {
                            labels: this.labels,
                            datasets: [
                                {
                                    label: 'Pemasukan (In)',
                                    data: this.income,
                                    borderColor: '#84CC16',
                                    backgroundColor: isLine ? incomeGradient : '#C6F24D',
                                    borderWidth: isLine ? 3 : 0,
                                    borderRadius: isLine ? 0 : 8,
                                    fill: isLine,
                                    tension: 0.4,
                                    pointBackgroundColor: '#84CC16',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: isLine ? 4 : 0,
                                    pointHoverRadius: 6,
                                    order: 1
                                },
                                {
                                    label: 'Pengeluaran (Out)',
                                    data: this.expense,
                                    borderColor: '#0F172A',
                                    backgroundColor: isLine ? expenseGradient : '#0F172A',
                                    borderWidth: isLine ? 2.5 : 0,
                                    borderRadius: isLine ? 0 : 8,
                                    fill: isLine,
                                    tension: 0.4,
                                    pointBackgroundColor: '#0F172A',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: isLine ? 4 : 0,
                                    pointHoverRadius: 6,
                                    order: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 400
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#090D16',
                                    titleColor: '#F8F9FA',
                                    titleFont: {
                                        family: 'Plus Jakarta Sans',
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    bodyColor: '#E2E8F0',
                                    bodyFont: {
                                        family: 'JetBrains Mono',
                                        size: 11
                                    },
                                    padding: 12,
                                    cornerRadius: 12,
                                    boxPadding: 4,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += new Intl.NumberFormat('id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR',
                                                    maximumFractionDigits: 0
                                                }).format(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Plus Jakarta Sans',
                                            size: 11,
                                            weight: '600'
                                        },
                                        color: '#64748B'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.7)',
                                        borderDash: [4, 4],
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            family: 'JetBrains Mono',
                                            size: 10,
                                            weight: '600'
                                        },
                                        color: '#94A3B8',
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                            }
                                            return 'Rp ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            };
        }
    </script>
    </div>

    <!-- Role-Specific Smart Modals -->
    <livewire:tools.student-split-bill />
    <livewire:tools.merchant-pricing-calculator />
</div>
