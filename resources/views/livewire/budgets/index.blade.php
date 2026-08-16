<div class="space-y-4 sm:space-y-6">
    
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  1. TOP BANNER: INCOME FLOOR & VOLATILITY ENGINE (PRD v1.2) -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-xs space-y-4 sm:space-y-6">
        
        <!-- Header & Action Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 border-b border-slate-100 pb-4 sm:pb-5">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-[#EBFAD2] text-slate-900 border border-[#D4F66C]">
                        PRD v1.2 Engine
                    </span>
                    <h2 class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight">Adaptive Budget Allocation</h2>
                </div>
                <p class="text-[11px] sm:text-xs text-slate-400">Baseline Income Floor (P25) & Zero-Based Surplus Waterfall</p>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center shrink-0">
                <input type="month" wire:model.live="selectedMonth" 
                    class="w-full sm:w-auto bg-[#F8F9FA] border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-slate-900">
                
                <button wire:click="openConfigModal" 
                    type="button"
                    class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-extrabold shadow-2xs active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <x-icon name="settings" class="w-3.5 h-3.5" />
                    <span>Atur Tier</span>
                </button>
            </div>
        </div>

        <!-- 4 Metric Cards: Floor, CV, Income, Surplus -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
            
            <!-- Income Floor (P25) -->
            <div class="p-3 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Income Floor</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded">P25</span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-slate-950 truncate">
                    Rp {{ number_format($budgetData['income_floor'], 0, ',', '.') }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">Baseline 12 bln</span>
            </div>

            <!-- Volatility (CV) & Method -->
            <div class="p-3 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Volatilitas</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold {{ $budgetData['floor_data']['is_volatile'] ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }} px-1.5 py-0.5 rounded">
                        {{ $budgetData['floor_data']['cv_value'] }}
                    </span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-slate-950 capitalize truncate">
                    {{ $budgetData['active_method'] }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">{{ $budgetData['floor_data']['is_volatile'] ? 'Income Fluktuatif' : 'Relatif Stabil' }}</span>
            </div>

            <!-- Pemasukan Aktual Bulan Ini -->
            <div class="p-3 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Pemasukan Riil</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded">Aktual</span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-emerald-600 truncate">
                    Rp {{ number_format($budgetData['current_income'], 0, ',', '.') }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">{{ date('M Y', strtotime($selectedMonth . '-01')) }}</span>
            </div>

            <!-- Surplus di Atas Floor -->
            <div class="p-3 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Surplus</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold bg-[#C6F24D] text-slate-950 px-1.5 py-0.5 rounded">Sisa</span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-slate-950 truncate">
                    +Rp {{ number_format($budgetData['surplus_waterfall']['surplus_amount'], 0, ',', '.') }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">Income − Floor</span>
            </div>

        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  2. SURPLUS WATERFALL ALLOCATION (When Income > Floor)       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    @if($budgetData['surplus_waterfall']['surplus_amount'] > 0)
    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-xs space-y-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-[#84CC16]"></span>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Alokasi Surplus Waterfall</h3>
            </div>
            <p class="text-[11px] sm:text-xs text-slate-400">Pencegah Lifestyle Inflation: dialokasikan bertingkat sesuai prioritas</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
            @foreach($budgetData['surplus_waterfall']['steps'] as $step)
            <div class="p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="w-5 h-5 rounded-full bg-slate-950 text-[#C6F24D] flex items-center justify-center font-mono font-black text-[10px]">
                            {{ $step['order'] }}
                        </span>
                        <span class="text-[9px] font-bold font-mono px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-700">
                            {{ $step['badge'] }}
                        </span>
                    </div>
                    <span class="font-extrabold text-xs text-slate-900 block leading-tight">{{ $step['title'] }}</span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $step['target'] }}</span>
                </div>

                <div class="pt-2 border-t border-slate-200/60 flex items-baseline justify-between">
                    <span class="text-xs sm:text-sm font-black font-mono text-slate-950">
                        Rp {{ number_format($step['amount'], 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] font-bold font-mono text-slate-500">({{ $step['percentage'] }}%)</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  3. GROUP & PRIORITY TIER BUDGET ALLOCATION BREAKDOWN       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="space-y-3 sm:space-y-4">
        <div>
            <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Rincian Budget per Group & Priority Tier</h3>
            <p class="text-[11px] sm:text-xs text-slate-400">Total Alokasi Target: <strong>{{ $budgetData['total_target_percentage'] }}%</strong> dari Base Income (Rp {{ number_format($budgetData['base_budget_income'], 0, ',', '.') }})</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-5">
            @foreach($budgetData['group_breakdown'] as $group)
            <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 flex flex-col justify-between">
                
                <!-- Group Header -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2.5 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800 shrink-0">
                                <x-icon :name="$group['icon']" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-tight truncate">{{ $group['name'] }}</h4>
                                <span class="text-[9px] sm:text-[10px] font-mono text-slate-400 truncate block">Alokasi {{ $group['target_percentage'] }}% &bull; Cap: Rp {{ number_format($group['budget_cap'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="text-right font-mono shrink-0">
                            <span class="text-xs font-black text-slate-900 block">Rp {{ number_format($group['actual_spent'], 0, ',', '.') }}</span>
                            <span class="text-[9px] sm:text-[10px] text-slate-400">{{ $group['progress_percentage'] }}% terpakai</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-3">
                        <div class="h-full rounded-full transition-all {{ $group['progress_percentage'] > 100 ? 'bg-rose-500' : 'bg-slate-950' }}" 
                            style="width: {{ min(100, $group['progress_percentage']) }}%"></div>
                    </div>

                    <!-- Sub Categories in this Group -->
                    <div class="space-y-1.5 pt-0.5">
                        @foreach($group['categories'] as $cat)
                        <div class="p-2 sm:p-2.5 bg-[#F8F9FA] rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5 sm:gap-2 min-w-0 pr-2">
                                <span class="px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-bold shrink-0 {{ $cat['priority_tier'] === 1 ? 'bg-rose-100 text-rose-800' : ($cat['priority_tier'] === 2 ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                    T{{ $cat['priority_tier'] }}
                                </span>
                                <span class="font-bold text-[11px] sm:text-xs text-slate-900 truncate">{{ $cat['name'] }}</span>
                                <span class="text-[9px] sm:text-[10px] font-mono text-slate-400 shrink-0">({{ $cat['target_percentage'] }}%)</span>
                            </div>
                            <div class="text-right font-mono shrink-0">
                                <span class="font-extrabold text-slate-900 block text-[10px] sm:text-[11px]">Rp {{ number_format($cat['actual_spent'], 0, ',', '.') }}</span>
                                <span class="text-[8px] sm:text-[9px] text-slate-400">Cap: Rp {{ number_format($cat['budget_cap'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  4. CONFIGURATION & WIZARD MODAL (PRD Addendum v1.2)        -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-data="{ open: @entangle('isConfigModalOpen') }" x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        
        <div @click.outside="$wire.set('isConfigModalOpen', false)" 
            class="relative w-full max-w-3xl bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

            <!-- Modal Header -->
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Setting Budget Engine</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400">Atur mapping Group, Priority Tier, dan Target (%)</p>
                </div>
                <button wire:click="$set('isConfigModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- Modal Body Form -->
            <div class="p-4 sm:p-6 overflow-y-auto space-y-4">
                
                @if (session()->has('config_message'))
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-800 flex items-center gap-2">
                        <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{{ session('config_message') }}</span>
                    </div>
                @endif

                @error('total_percentage')
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-800 flex items-center gap-2">
                        <x-icon name="alert-circle" class="w-4 h-4 text-rose-600 shrink-0" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <!-- Profile Name & Method Controls -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Profil Budget</label>
                        <input type="text" wire:model.defer="profileName" 
                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Perhitungan</label>
                        <select wire:model="method" 
                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                            <option value="floor">Floor Method (Freelance CV ≥ 0.3)</option>
                            <option value="average">Average Method (Income Stabil)</option>
                        </select>
                    </div>
                </div>

                <!-- Auto-Suggest EMA Button -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-3.5 bg-[#EBFAD2] border border-[#D4F66C] rounded-xl sm:rounded-2xl gap-2.5">
                    <div class="flex items-center gap-2">
                        <x-icon name="sparkles" class="w-4 h-4 text-[#84CC16] shrink-0" />
                        <div>
                            <span class="text-xs font-extrabold text-slate-900 block">Saran Otomatis (EMA 6-Bulan)</span>
                            <span class="text-[10px] text-slate-600">Hitung proporsi historis aktual tanpa tebak-tebakan</span>
                        </div>
                    </div>
                    <button type="button" wire:click="applyEmaSuggestions" 
                        class="w-full sm:w-auto px-3 py-1.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 transition-colors shadow-2xs cursor-pointer text-center">
                        Terapkan Saran &rarr;
                    </button>
                </div>

                <!-- Table Categories Config -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-extrabold text-slate-700 px-1">
                        <span>Daftar Kategori</span>
                        <span>Target: <strong class="font-mono text-slate-900">{{ array_sum(array_column($categoryConfigs, 'target_percentage')) }}%</strong> / 100%</span>
                    </div>

                    <div class="space-y-2">
                        @foreach($categories as $cat)
                        @php
                            $cfg = $categoryConfigs[$cat->id] ?? null;
                            $z = $zScores[$cat->id] ?? null;
                        @endphp
                        @if($cfg)
                        <div class="p-3 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900 truncate">{{ $cat->name }}</span>
                                @if($z)
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full {{ $z['status'] === 'realistic' ? 'bg-emerald-100 text-emerald-800' : ($z['status'] === 'moderate' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $z['badge'] }}
                                </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div>
                                    <label class="text-[9px] sm:text-[10px] font-bold text-slate-500 block mb-0.5">Budget Group</label>
                                    <select wire:model="categoryConfigs.{{ $cat->id }}.budget_group_id" 
                                        class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-slate-900">
                                        @foreach($groups as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[9px] sm:text-[10px] font-bold text-slate-500 block mb-0.5">Priority Tier</label>
                                    <select wire:model="categoryConfigs.{{ $cat->id }}.priority_tier" 
                                        class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-800 focus:outline-none focus:border-slate-900">
                                        <option value="1">Tier 1 — Critical (Wajib)</option>
                                        <option value="2">Tier 2 — Essential (Variabel)</option>
                                        <option value="3">Tier 3 — Discretionary (Wants)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[9px] sm:text-[10px] font-bold text-slate-500 block mb-0.5">Target Persentase (%)</label>
                                    <div class="relative">
                                        <input type="number" step="0.5" wire:model.live.debounce.300ms="categoryConfigs.{{ $cat->id }}.target_percentage" 
                                            class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-900 pr-7">
                                        <span class="absolute right-2.5 top-1.5 text-xs font-bold text-slate-400">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 border-t border-slate-100 bg-white flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500">
                    Total: <strong class="font-mono text-slate-900">{{ array_sum(array_column($categoryConfigs, 'target_percentage')) }}%</strong>
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('isConfigModalOpen', false)" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="saveConfiguration" 
                        class="px-4 py-2 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 cursor-pointer shadow-sm">
                        Simpan
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
