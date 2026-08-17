<div class="space-y-4 sm:space-y-6">
    
    <!-- Flash Notification Banner -->
    @if (session()->has('message'))
    <div class="p-3 sm:p-4 bg-emerald-50 border-2 border-emerald-200 rounded-2xl flex items-center justify-between gap-3 text-xs text-emerald-900 font-bold shadow-xs anim-fade-up">
        <div class="flex items-center gap-2">
            <x-icon name="check" class="w-4 h-4 text-emerald-600 shrink-0" strokeWidth="3" />
            <span>{{ session('message') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
            <x-icon name="x" class="w-3.5 h-3.5" />
        </button>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  1. TOP BANNER: INCOME FLOOR & VOLATILITY ENGINE (UNIVERSAL)-->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-xs space-y-4 sm:space-y-6">
        
        <!-- Header & Action Row -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5 border-b border-slate-100 pb-4 sm:pb-5">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-[#EBFAD2] text-slate-900 border border-[#D4F66C]">
                        Smart Engine
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-black uppercase bg-slate-950 text-[#C6F24D]">
                        Profil: {{ $profileName }}
                    </span>
                </div>
                <h2 class="text-base sm:text-xl font-extrabold text-slate-950 tracking-tight">Adaptive Budget Allocation</h2>
                <p class="text-[11px] sm:text-xs text-slate-400">Penganggaran Adaptif Berdasarkan Arketipe Finansial & Surplus Waterfall</p>
            </div>

            <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 shrink-0">
                <button wire:click="openSurveyModal" 
                    type="button"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#F4FCE3] hover:bg-[#EBFAD2] text-slate-950 text-xs font-black border border-[#D4F66C] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs active-tap">
                    <x-icon name="sparkles" class="w-4 h-4 text-[#84CC16]" strokeWidth="2.5" />
                    <span>Survei Profil</span>
                </button>

                <button wire:click="openConfigModal" 
                    type="button"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-black shadow-2xs active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <x-icon name="settings" class="w-3.5 h-3.5" />
                    <span>Atur Persentase</span>
                </button>

                <div class="col-span-2 sm:col-span-1">
                    <input type="month" wire:model.live="selectedMonth" 
                        class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-slate-950 cursor-pointer">
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards: Floor, CV, Income, Surplus -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
            
            <!-- Income Floor (P25) -->
            <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Income Floor</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded">P25</span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-slate-950 truncate">
                    Rp {{ number_format($budgetData['income_floor'], 0, ',', '.') }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">Baseline Aman</span>
            </div>

            <!-- Volatility (CV) & Method -->
            <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Karakter Kas</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold {{ $budgetData['floor_data']['is_volatile'] ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }} px-1.5 py-0.5 rounded">
                        {{ $budgetData['floor_data']['cv_value'] }}
                    </span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-slate-950 capitalize truncate">
                    {{ $budgetData['active_method'] }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">{{ $budgetData['floor_data']['is_volatile'] ? 'Fluktuatif' : 'Stabil' }}</span>
            </div>

            <!-- Pemasukan Aktual Bulan Ini -->
            <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
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
            <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 truncate">Surplus / Selisih</span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-extrabold bg-[#C6F24D] text-slate-950 px-1.5 py-0.5 rounded">Sisa</span>
                </div>
                <div class="text-sm sm:text-lg font-black font-mono text-slate-950 truncate">
                    +Rp {{ number_format($budgetData['surplus_waterfall']['surplus_amount'], 0, ',', '.') }}
                </div>
                <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">Di Atas Floor</span>
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
                <span class="w-2.5 h-2.5 rounded-full bg-[#84CC16] animate-pulse"></span>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Alokasi Surplus Waterfall</h3>
            </div>
            <p class="text-[11px] sm:text-xs text-slate-400">Pencegah Lifestyle Inflation: surplus dialokasikan bertingkat sesuai target prioritas</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
            @foreach($budgetData['surplus_waterfall']['steps'] as $step)
            <div class="p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="w-6 h-6 rounded-full bg-slate-950 text-[#C6F24D] flex items-center justify-center font-mono font-black text-[10px]">
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
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Rincian Budget Pos & Priority Tier</h3>
                <p class="text-[11px] sm:text-xs text-slate-400">Total Alokasi Target: <strong class="text-slate-900 font-mono">{{ $budgetData['total_target_percentage'] }}%</strong> dari Base Income (Rp {{ number_format($budgetData['base_budget_income'], 0, ',', '.') }})</p>
            </div>
            <button wire:click="openConfigModal" type="button" class="text-xs font-extrabold text-slate-950 hover:underline flex items-center gap-1 cursor-pointer">
                <span>Sesuaikan Slider</span> &rarr;
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-5">
            @foreach($budgetData['group_breakdown'] as $group)
            <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 flex flex-col justify-between hover:border-slate-300 transition-all">
                
                <!-- Group Header -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2.5 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800 shrink-0">
                                <x-icon :name="$group['icon']" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-tight truncate">{{ $group['name'] }}</h4>
                                <span class="text-[9px] sm:text-[10px] font-mono text-slate-400 truncate block">Alokasi {{ $group['target_percentage'] }}% &bull; Batas: Rp {{ number_format($group['budget_cap'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="text-right font-mono shrink-0">
                            <span class="text-xs font-black text-slate-900 block">Rp {{ number_format($group['actual_spent'], 0, ',', '.') }}</span>
                            <span class="text-[9px] sm:text-[10px] text-slate-400">{{ $group['progress_percentage'] }}% terpakai</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mb-3">
                        <div class="h-2 rounded-full transition-all duration-500 {{ $group['progress_percentage'] > 100 ? 'bg-rose-500' : ($group['progress_percentage'] > 85 ? 'bg-amber-500' : 'bg-slate-950') }}" 
                            style="width: {{ min(100, $group['progress_percentage']) }}%"></div>
                    </div>

                    <!-- Categories list within this group -->
                    <div class="space-y-1.5 pt-1">
                        @foreach($group['categories'] as $catItem)
                        <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $catItem['priority_tier'] == 1 ? 'bg-rose-500' : ($catItem['priority_tier'] == 2 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                <span class="font-bold text-slate-900 truncate">{{ $catItem['name'] }}</span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">T{{ $catItem['priority_tier'] }}</span>
                            </div>
                            <div class="text-right font-mono text-[11px] shrink-0">
                                <span class="font-black text-slate-950">Rp {{ number_format($catItem['actual_spent'], 0, ',', '.') }}</span>
                                <span class="text-slate-400">/ Rp {{ number_format($catItem['budget_cap'] ?? 0, 0, ',', '.') }} ({{ $catItem['target_percentage'] }}%)</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Summary of Group -->
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                    <span>Sisa Limit: <strong class="font-mono text-slate-800">Rp {{ number_format(max(0, $group['budget_cap'] - $group['actual_spent']), 0, ',', '.') }}</strong></span>
                    <span class="font-bold {{ $group['progress_percentage'] > 100 ? 'text-rose-600' : 'text-emerald-700' }}">
                        {{ $group['progress_percentage'] > 100 ? 'Overbudget' : 'Terkendali' }}
                    </span>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MODAL 1: SMART FINANCIAL SURVEY (UNIVERSAL ARCHETYPES)    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-data="{ open: @entangle('isSurveyModalOpen') }" x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" 
        x-cloak>
        
        <div @click.outside="$wire.set('isSurveyModalOpen', false)" 
            class="relative w-full max-w-2xl bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col anim-scale-up">
            
            <!-- Drag bar (mobile) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

            <!-- Survey Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black shadow-2xs">
                        <x-icon name="sparkles" class="w-5 h-5" strokeWidth="2.5" />
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-950">Smart Financial Persona Wizard</h3>
                        <p class="text-[10px] sm:text-xs text-slate-400">Langkah {{ $surveyStep }} dari 3: Sesuaikan arketipe dan alokasi dana ideal</p>
                    </div>
                </div>
                <button wire:click="$set('isSurveyModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- Visual Step Breadcrumb -->
            <div class="grid grid-cols-3 gap-1.5 px-5 sm:px-6 py-2.5 bg-slate-50 border-b border-slate-100 text-center text-[10px] font-bold">
                <div class="py-1 rounded-xl transition-all {{ $surveyStep >= 1 ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'bg-slate-200 text-slate-500' }}">
                    1. Arketipe Anda
                </div>
                <div class="py-1 rounded-xl transition-all {{ $surveyStep >= 2 ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'bg-slate-200 text-slate-500' }}">
                    2. Karakter Kas
                </div>
                <div class="py-1 rounded-xl transition-all {{ $surveyStep >= 3 ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'bg-slate-200 text-slate-500' }}">
                    3. Target 6 Bulan
                </div>
            </div>

            <!-- Survey Body -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-4">
                
                <!-- STEP 1: PILIH ARKETIPE -->
                @if($surveyStep === 1)
                <div class="space-y-3">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-950">Siapa Anda & Dari Mana Sumber Pendapatan Utama?</h4>
                        <p class="text-xs text-slate-500">Pilih profil yang paling mendekati situasi kehidupan Anda saat ini:</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        @foreach($personas as $key => $p)
                        <button type="button" 
                            wire:click="selectSurveyPersona('{{ $key }}')"
                            class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group flex items-start gap-3.5 {{ $selectedPersona === $key ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200/90 hover:border-slate-400 bg-white hover:bg-slate-50' }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs {{ $selectedPersona === $key ? 'bg-slate-950 text-[#C6F24D]' : 'bg-slate-100 text-slate-700' }}">
                                <x-icon :name="$p['icon']" class="w-5 h-5" strokeWidth="2.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-black text-slate-950 block group-hover:text-teal-700 transition-colors leading-snug">{{ $p['name'] }}</span>
                                <span class="text-[10px] text-slate-500 leading-tight block mt-1">{{ $p['description'] }}</span>
                                <span class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-[8px] font-mono font-bold bg-white border border-slate-200 text-slate-700">
                                    {{ $p['badge'] }}
                                </span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- STEP 2: STABILITAS KAS -->
                @if($surveyStep === 2)
                <div class="space-y-3">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-950">Bagaimana Karakter / Pola Pendapatan Anda?</h4>
                        <p class="text-xs text-slate-500">Menentukan metode kalkulasi baseline aman (Floor Baseline P25 vs Rolling Mean):</p>
                    </div>

                    <div class="space-y-2.5 pt-1">
                        <!-- Option 1: Volatile -->
                        <button type="button" 
                            wire:click="selectSurveyStability('volatile')"
                            class="w-full p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedStability === 'volatile' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center shrink-0 font-bold text-lg">
                                🌊
                            </div>
                            <div>
                                <span class="text-xs font-extrabold text-slate-950 block">Fluktuatif / Project-Based / Omset Dagang</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pendapatan naik-turun tiap bulan. Sistem mengaktifkan <strong>Floor Baseline (P25)</strong> agar Anda tidak kekurangan di bulan sepi.</p>
                            </div>
                        </button>

                        <!-- Option 2: Semi-stable -->
                        <button type="button" 
                            wire:click="selectSurveyStability('semi')"
                            class="w-full p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedStability === 'semi' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-900 flex items-center justify-center shrink-0 font-bold text-lg">
                                ⚖️
                            </div>
                            <div>
                                <span class="text-xs font-extrabold text-slate-950 block">Kombinasi Retainer & Sampingan</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Ada pendapatan minimum pasti ditambah bonus projek. Mengaktifkan <strong>Hybrid Smoothing Engine</strong>.</p>
                            </div>
                        </button>

                        <!-- Option 3: Stable -->
                        <button type="button" 
                            wire:click="selectSurveyStability('stable')"
                            class="w-full p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedStability === 'stable' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center shrink-0 font-bold text-lg">
                                📅
                            </div>
                            <div>
                                <span class="text-xs font-extrabold text-slate-950 block">Stabil / Gaji Bulanan Terjadwal</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Menerima transfer gaji di tanggal yang sama. Mengaktifkan <strong>50/30/20 Standard Zero-Based</strong>.</p>
                            </div>
                        </button>
                    </div>
                </div>
                @endif

                <!-- STEP 3: PRIORITAS TARGET 6 BULAN -->
                @if($surveyStep === 3)
                <div class="space-y-3">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-950">Apa Target Finansial Utama Anda 6 Bulan Kedepan?</h4>
                        <p class="text-xs text-slate-500">Sistem akan mengoptimalkan persentase tabungan pos terkait secara otomatis:</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        <!-- Priority 1 -->
                        <button type="button" 
                            wire:click="selectSurveyPriority('emergency')"
                            class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedPriority === 'emergency' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-base">🛡️</div>
                            <div>
                                <span class="text-xs font-bold text-slate-950 block">Dana Darurat & Pajak</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Amankan cadangan hidup 3-6 bulan</span>
                            </div>
                        </button>

                        <!-- Priority 2 -->
                        <button type="button" 
                            wire:click="selectSurveyPriority('wishlist')"
                            class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedPriority === 'wishlist' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center shrink-0 font-bold text-base">🎯</div>
                            <div>
                                <span class="text-xs font-bold text-slate-950 block">Wishlist & Upgrade Alat</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Kejar target barang / gadget impian</span>
                            </div>
                        </button>

                        <!-- Priority 3 -->
                        <button type="button" 
                            wire:click="selectSurveyPriority('investment')"
                            class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedPriority === 'investment' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0 font-bold text-base">📈</div>
                            <div>
                                <span class="text-xs font-bold text-slate-950 block">Investasi & Portofolio</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Maksimalkan pertumbuhan aset</span>
                            </div>
                        </button>

                        <!-- Priority 4 -->
                        <button type="button" 
                            wire:click="selectSurveyPriority('separate')"
                            class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-start gap-3.5 {{ $selectedPriority === 'separate' ? 'border-slate-950 bg-[#F4FCE3] shadow-xs' : 'border-slate-200 hover:border-slate-400 bg-white' }}">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 font-bold text-base">⚖️</div>
                            <div>
                                <span class="text-xs font-bold text-slate-950 block">Pemisahan Kas Usaha</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Disiplin modal dagang vs laba bersih</span>
                            </div>
                        </button>
                    </div>
                </div>
                @endif

            </div>

            <!-- Survey Modal Footer -->
            <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    @if($surveyStep > 1)
                    <button type="button" wire:click="setSurveyStep({{ $surveyStep - 1 }})" class="text-xs font-bold text-slate-500 hover:text-slate-900 cursor-pointer flex items-center gap-1">
                        &larr; Kembali
                    </button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('isSurveyModalOpen', false)" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-900 cursor-pointer">
                        Batal
                    </button>

                    @if($surveyStep < 3)
                    <button type="button" wire:click="setSurveyStep({{ $surveyStep + 1 }})" class="px-4 py-2 rounded-xl bg-slate-950 text-white text-xs font-extrabold hover:bg-slate-800 cursor-pointer shadow-xs">
                        Lanjut &rarr;
                    </button>
                    @else
                    <button type="button" wire:click="submitSurvey" class="px-5 py-2.5 rounded-xl bg-[#C6F24D] hover:bg-[#B5E63B] text-slate-950 text-xs font-black cursor-pointer shadow-md active-tap">
                        ✓ Terapkan & Buat Anggaran Otomatis
                    </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MODAL 2: INTERACTIVE PERCENTAGE SLIDERS & ENGINE CONFIG    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-data="{ open: @entangle('isConfigModalOpen') }" x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" 
        x-cloak>
        
        <div @click.outside="$wire.set('isConfigModalOpen', false)" 
            class="relative w-full max-w-2xl bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col anim-scale-up">
            
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2 shrink-0"></div>

            <!-- Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black shadow-2xs shrink-0">
                        <x-icon name="sliders" class="w-5 h-5" strokeWidth="2.2" />
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-950 tracking-tight">Atur Persentase Alokasi Budget</h3>
                        <p class="text-xs text-slate-500">Sesuaikan porsi target, edit nama pos, atau tambah pos alokasi baru</p>
                    </div>
                </div>
                <button wire:click="$set('isConfigModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- BODY -->
            <div class="p-5 sm:p-6 space-y-4.5 overflow-y-auto">
                
                <!-- 1. MULTI-SEGMENT LIVING ALLOCATION BAR (TOTAL 100%) -->
                @php
                    $totalPct = array_sum(array_column($categoryConfigs, 'target_percentage'));
                @endphp
                <div class="p-4.5 bg-slate-50 border border-slate-200/90 rounded-2xl space-y-3 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold text-slate-900">Total Alokasi:</span>
                            <span class="px-2.5 py-0.5 rounded-full font-mono text-xs font-black {{ abs($totalPct - 100) < 0.1 ? 'bg-emerald-100 text-emerald-800' : ($totalPct > 100 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ round($totalPct, 1) }}% / 100%
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="autoBalanceAllocation" 
                                class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-slate-950 text-slate-800 text-[11px] font-extrabold shadow-2xs transition-all cursor-pointer flex items-center gap-1.5 active-tap">
                                <span>⚡ Seimbangkan 100%</span>
                            </button>
                            <button type="button" wire:click="applyEmaSuggestions" 
                                class="px-3 py-1.5 rounded-xl bg-slate-950 text-[#C6F24D] text-[11px] font-extrabold hover:bg-slate-800 shadow-2xs transition-all cursor-pointer flex items-center gap-1.5 active-tap">
                                <x-icon name="sparkles" class="w-3 h-3 text-[#C6F24D]" />
                                <span>Saran Historis</span>
                            </button>
                        </div>
                    </div>

                    <!-- Living Segmented Progress Bar -->
                    <div class="w-full bg-slate-200 rounded-full h-3.5 overflow-hidden flex shadow-inner">
                        @foreach($categoryConfigs as $catId => $cfg)
                        @php
                            $pct = (float) ($cfg['target_percentage'] ?? 0);
                            $groupModel = $groups->firstWhere('id', $cfg['budget_group_id']);
                            $color = $groupModel?->color ?? '#64748B';
                        @endphp
                        @if($pct > 0)
                        <div class="h-full transition-all duration-300 relative group" 
                            style="width: {{ $pct }}%; background-color: {{ $color }};" 
                            title="{{ $cfg['name'] ?? 'Kategori' }}: {{ $pct }}%">
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <!-- Status Hint -->
                    <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-500 pt-0.5 gap-2">
                        <span>
                            @if(abs($totalPct - 100) < 0.1)
                                <strong class="text-emerald-700 font-bold">✓ Alokasi 100% Sempurna & Seimbang</strong>
                            @elseif($totalPct > 100)
                                <strong class="text-rose-600 font-bold">⚠ Kelebihan {{ round($totalPct - 100, 1) }}% — Kurangi slider beberapa pos</strong>
                            @else
                                <strong class="text-amber-700 font-bold">⚠ Sisa {{ round(100 - $totalPct, 1) }}% belum dialokasikan</strong>
                            @endif
                        </span>
                        <span class="font-mono text-slate-500 font-bold">Base Income: Rp {{ number_format($budgetData['base_budget_income'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- 2. PROFILE NAME & METHOD -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">Nama Profil Anggaran</label>
                        <input type="text" wire:model="profileName" 
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">Metode Baseline Kas</label>
                        <select wire:model="method" 
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950 focus:bg-white cursor-pointer transition-all">
                            <option value="floor">Floor Baseline (P25) — Untuk Pendapatan Fluktuatif</option>
                            <option value="average">Rolling Average — Untuk Gaji Bulanan Teratur</option>
                        </select>
                    </div>
                </div>

                <!-- 3. CUSTOM CATEGORIES SECTION & ADD BUTTON -->
                <div class="space-y-3 pt-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-extrabold text-slate-900 block">Daftar Pos Alokasi</label>
                            <span class="text-[10px] text-slate-400">{{ count($categoryConfigs) }} Pos Terdaftar — Klik nama untuk mengedit</span>
                        </div>
                        <button type="button" wire:click="toggleAddCategory"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer {{ $isAddingCategory ? 'bg-slate-200 text-slate-800' : 'bg-[#C6F24D] text-slate-950 hover:bg-[#B5E63B] shadow-2xs' }}">
                            <x-icon :name="$isAddingCategory ? 'x' : 'plus'" class="w-3.5 h-3.5" strokeWidth="2.5" />
                            <span>{{ $isAddingCategory ? 'Tutup Form' : '+ Tambah Pos Baru' }}</span>
                        </button>
                    </div>

                    <!-- Expandable Add New Pos Form -->
                    @if($isAddingCategory)
                    <div class="p-4 bg-[#F7FFD9] border-2 border-slate-900 rounded-2xl space-y-3 animate-fade-in shadow-sm">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                                <x-icon name="plus-circle" class="w-4 h-4 text-slate-900" />
                                <span>Tambah Pos Alokasi Kategori Baru</span>
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold uppercase text-slate-700 block mb-1">Nama Pos / Kategori *</label>
                                <input type="text" wire:model="newCategoryName" placeholder="e.g. Investasi Saham / Netflix"
                                    class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-slate-950">
                                @error('newCategoryName') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-[10px] font-bold uppercase text-slate-700 block mb-1">Target Persentase (%) *</label>
                                <input type="number" step="0.5" min="0" max="100" wire:model="newCategoryPercentage" placeholder="5"
                                    class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-950">
                                @error('newCategoryPercentage') <span class="text-[10px] text-rose-600 font-bold block mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-[10px] font-bold uppercase text-slate-700 block mb-1">Kelompok Pos (Group) *</label>
                                <select wire:model="newCategoryGroupId"
                                    class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-950 cursor-pointer">
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold uppercase text-slate-700 block mb-1">Tingkat Prioritas (Tier) *</label>
                                <select wire:model="newCategoryTier"
                                    class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-950 cursor-pointer">
                                    <option value="1">Tier 1 — Wajib (Critical)</option>
                                    <option value="2">Tier 2 — Variabel (Essential)</option>
                                    <option value="3">Tier 3 — Keinginan (Wants)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1 border-t border-lime-300">
                            <button type="button" wire:click="toggleAddCategory" class="px-3 py-1.5 text-xs font-bold text-slate-700 hover:text-slate-950 cursor-pointer">
                                Batal
                            </button>
                            <button type="button" wire:click="addNewCategory" class="px-4 py-1.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 shadow-2xs active-tap cursor-pointer">
                                + Tambahkan ke Alokasi
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Category Sliders List -->
                    <div class="space-y-2.5">
                        @foreach($categoryConfigs as $catId => $cfg)
                        @php
                            $catPct = (float) ($cfg['target_percentage'] ?? 0);
                            $calcNominal = ($catPct / 100) * $budgetData['base_budget_income'];
                            $grp = $groups->firstWhere('id', $cfg['budget_group_id']);
                        @endphp
                        <div class="p-3.5 sm:p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-2.5 hover:border-slate-300 transition-all group">
                            
                            <!-- Header Item: Editable Name, Tag, Nominal, Percentage Badge, Trash Button -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $grp?->color ?? '#0F172A' }}"></span>
                                    
                                    <!-- Inline Editable Name -->
                                    <div class="relative flex-1 min-w-[140px] max-w-[280px]">
                                        <input type="text" 
                                            wire:model.lazy="categoryConfigs.{{ $catId }}.name"
                                            class="w-full text-xs font-extrabold text-slate-950 bg-transparent hover:bg-slate-100/70 focus:bg-white focus:ring-1 focus:ring-slate-950 border border-transparent hover:border-slate-200 focus:border-slate-950 rounded-lg px-1.5 py-0.5 transition-all truncate"
                                            title="Klik untuk mengubah nama pos">
                                    </div>

                                    <span class="text-[9px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 shrink-0 hidden sm:inline-block">
                                        {{ $grp?->name ?? 'Pos' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-mono text-xs font-extrabold text-slate-950">
                                        Rp {{ number_format($calcNominal, 0, ',', '.') }}
                                    </span>
                                    
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 text-[#C6F24D] font-mono text-xs font-black">
                                        {{ $catPct }}%
                                    </span>

                                    <!-- Delete Category from allocation -->
                                    <button type="button" 
                                        wire:click="removeCategory({{ $catId }})" 
                                        wire:confirm="Hapus pos kategori '{{ $cfg['name'] ?? 'ini' }}' dari alokasi budget?"
                                        class="p-1 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer ml-0.5" 
                                        title="Hapus dari alokasi">
                                        <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Interactive Smooth Range Slider -->
                            <div class="space-y-1">
                                <input type="range" min="0" max="60" step="0.5" 
                                    wire:model.live="categoryConfigs.{{ $catId }}.target_percentage" 
                                    class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-slate-950">
                            </div>

                            <!-- Controls: Group & Tier Selector -->
                            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                                <div>
                                    <select wire:model="categoryConfigs.{{ $catId }}.budget_group_id" 
                                        class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] font-semibold text-slate-800 focus:outline-none focus:border-slate-950 focus:bg-white cursor-pointer transition-all">
                                        @foreach($groups as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <select wire:model="categoryConfigs.{{ $catId }}.priority_tier" 
                                        class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] font-semibold text-slate-800 focus:outline-none focus:border-slate-950 focus:bg-white cursor-pointer transition-all">
                                        <option value="1">Tier 1 — Wajib (Critical)</option>
                                        <option value="2">Tier 2 — Variabel (Essential)</option>
                                        <option value="3">Tier 3 — Keinginan (Wants)</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-5 sm:px-6 py-4 border-t border-slate-100 bg-white flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500">
                    Total: <strong class="font-mono text-slate-950 font-black text-sm">{{ array_sum(array_column($categoryConfigs, 'target_percentage')) }}%</strong>
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('isConfigModalOpen', false)" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="saveConfiguration" 
                        class="px-5 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-black hover:bg-slate-800 cursor-pointer shadow-sm active-tap transition-all">
                        Simpan Alokasi
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
