<div class="space-y-4 sm:space-y-6 max-w-5xl mx-auto">
    
    <!-- HEADER INTRO (Clean Responsive FinTech Banner) -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs">
        <div class="flex items-center gap-3 sm:gap-3.5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-[#C6F24D] text-slate-950 flex items-center justify-center font-bold shadow-2xs shrink-0">
                <x-icon name="calculator" class="w-5 h-5 sm:w-6 sm:h-6" strokeWidth="2.5" />
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">Simulator "Can I Afford This?"</h2>
                <p class="text-[11px] sm:text-xs text-slate-400">Evaluasi instan sebelum checkout barang untuk memastikan Available Money & Dana Darurat aman.</p>
            </div>
        </div>
    </div>

    <!-- MAIN SIMULATOR 2-COL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-start">
        
        <!-- LEFT: SIMULATION INPUTS (5 COLS) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 sm:space-y-4">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 block border-b border-slate-100 pb-2.5 sm:pb-3">Input Simulasi Pembelian</span>

            <!-- Option to load from Wishlist -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih dari Item Wishlist (Opsional)</label>
                <select wire:model.live="selectedWishlistId" 
                        class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    <option value="">-- Simulasi Bebas Manual --</option>
                    @foreach($wishlists as $w)
                        <option value="{{ $w->id }}">{{ $w->name }} (Rp {{ number_format($w->current_price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Item Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang</label>
                <input type="text" 
                       wire:model.defer="itemName" 
                       placeholder="e.g. DJI Pocket 4 / Monitor 4K" 
                       class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
            </div>

            <!-- Purchase Price Input -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Barang (Rp) *</label>
                <div class="relative rounded-xl sm:rounded-2xl">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs sm:text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" 
                           step="any"
                           wire:model.live.debounce.300ms="purchasePrice" 
                           placeholder="8000000" 
                           class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 text-base sm:text-lg font-bold font-mono text-slate-950 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Dedicated Savings -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Dana Tabungan Khusus (Rp)</label>
                <div class="relative rounded-xl sm:rounded-2xl">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" 
                           step="any"
                           wire:model.live.debounce.300ms="dedicatedSavings" 
                           placeholder="0" 
                           class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl pl-10 sm:pl-12 pr-4 py-2 text-xs font-bold font-mono text-emerald-600 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Uang yang sudah dialokasikan khusus untuk barang ini.</p>
            </div>

            <!-- Baseline Snapshot -->
            <div class="p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1.5 text-xs">
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Kondisi Real-Time Saat Ini:</span>
                <div class="flex items-center justify-between font-mono">
                    <span class="text-slate-500 font-sans text-[11px]">Total Saldo:</span>
                    <span class="font-bold text-slate-900 text-[11px] sm:text-xs">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between font-mono">
                    <span class="text-slate-500 font-sans text-[11px]">Available Money:</span>
                    <span class="font-bold text-emerald-600 text-[11px] sm:text-xs">Rp {{ number_format($availableMoney, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between font-mono">
                    <span class="text-slate-500 font-sans text-[11px]">Dana Darurat:</span>
                    <span class="font-bold text-slate-900 text-[11px] sm:text-xs">{{ $emergencyMonths }} bulan</span>
                </div>
            </div>
        </div>

        <!-- RIGHT: SIMULATION RESULT (7 COLS) -->
        <div class="lg:col-span-7 space-y-4 sm:space-y-5">
            @if(!empty($simulationResult))
            <!-- RECOMMENDATION CARD -->
            <div class="border rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs {{ $simulationResult['recommendation'] === 'SAFE' ? 'border-emerald-200 bg-emerald-50/60' : ($simulationResult['recommendation'] === 'CAUTION' ? 'border-amber-200 bg-amber-50/60' : 'border-rose-200 bg-rose-50/60') }}">
                
                <div class="flex items-center gap-3">
                    <span class="text-2xl sm:text-3xl">
                        @if($simulationResult['recommendation'] === 'SAFE') 🟢 @elseif($simulationResult['recommendation'] === 'CAUTION') 🟡 @else 🔴 @endif
                    </span>
                    <div>
                        <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">Hasil Evaluasi Kelayakan</span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                            {{ $simulationResult['title'] }}
                        </h3>
                    </div>
                </div>

                <p class="mt-2.5 text-xs text-slate-700 leading-relaxed font-medium">
                    {{ $simulationResult['description'] }}
                </p>
            </div>

            <!-- FINANCIAL IMPACT METRICS -->
            <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 sm:space-y-4">
                <h4 class="text-xs font-bold text-slate-900 tracking-tight flex items-center gap-1.5">
                    <x-icon name="activity" class="w-4 h-4 text-slate-700" />
                    <span>Dampak Finansial Sebelum vs Sesudah Beli</span>
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <!-- AVAILABLE MONEY IMPACT -->
                    <div class="p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                        <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium block">Available Money</span>
                        <div class="mt-1.5 flex items-center justify-between font-mono">
                            <span class="text-[11px] text-slate-400 line-through">Rp {{ number_format($simulationResult['metrics']['available_money_before'], 0, ',', '.') }}</span>
                            <span class="text-sm sm:text-base font-extrabold {{ $simulationResult['metrics']['available_money_after'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Rp {{ number_format($simulationResult['metrics']['available_money_after'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- EMERGENCY FUND IMPACT -->
                    <div class="p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                        <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium block">Ketahanan Dana Darurat</span>
                        <div class="mt-1.5 flex items-center justify-between font-mono">
                            <span class="text-[11px] text-slate-400 line-through">{{ $simulationResult['metrics']['emergency_months_before'] }} bln</span>
                            <span class="text-sm sm:text-base font-extrabold {{ $simulationResult['metrics']['emergency_months_after'] >= 3 ? 'text-emerald-600' : ($simulationResult['metrics']['emergency_months_after'] >= 1.5 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $simulationResult['metrics']['emergency_months_after'] }} bln
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-slate-950 text-white rounded-xl sm:rounded-2xl flex items-center justify-between text-xs font-mono">
                    <span class="text-slate-400 font-sans text-[11px]">Sisa Saldo Kas Bersih:</span>
                    <span class="font-bold text-[#C6F24D]">
                        Rp {{ number_format($simulationResult['metrics']['current_balance_after'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
