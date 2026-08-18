<div class="space-y-4 sm:space-y-6">
    
    <!-- TOP SUMMARY & ACTIONS -->
    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Likuiditas Seluruh Akun</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-slate-950 mt-0.5 tracking-tight">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 font-medium">Tersimpan di {{ $accounts->count() }} rekening bank & e-wallet aktif</p>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
            <button wire:click="openTransferModal" 
                    type="button"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl sm:rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs active-tap">
                <x-icon name="arrow-right-left" class="w-3.5 h-3.5 text-slate-700" strokeWidth="2.5" />
                <span>Transfer</span>
            </button>
            <button wire:click="openCreateModal" 
                    type="button"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl sm:rounded-2xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-black shadow-2xs active-tap transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span class="w-4 h-4 rounded-full bg-[#C6F24D] text-slate-950 flex items-center justify-center text-[10px] font-black">+</span>
                <span>Tambah Akun</span>
            </button>
        </div>
    </div>

    <!-- ACCOUNT CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">
        @foreach($accounts as $acc)
        <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:shadow-md hover:border-slate-400/80 transition-all group relative">
            <div>
                <!-- Top Header with Real Brand Logo -->
                <div class="flex items-center justify-between mb-3.5">
                    <div class="flex items-center gap-3 min-w-0">
                        <x-account-logo :name="$acc->name" :type="$acc->type" class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl shrink-0" />
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-sm sm:text-base text-slate-950 group-hover:text-teal-700 transition-colors truncate">{{ $acc->name }}</h4>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] font-mono font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 uppercase">{{ $acc->type }}</span>
                                @if($acc->account_number)
                                <span class="text-[10px] font-mono text-slate-400 truncate">{{ $acc->account_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Dropdown / Edit -->
                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="openEditModal({{ $acc->id }})" 
                                type="button"
                                title="Edit Akun"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-950 hover:bg-slate-100 transition-colors cursor-pointer">
                            <x-icon name="edit" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Balance Display -->
                <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 block">Saldo Akun:</span>
                    <span class="text-lg sm:text-xl font-black font-mono text-slate-950 block mt-0.5">
                        Rp {{ number_format($acc->current_balance, 0, ',', '.') }}
                    </span>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2 pt-2 border-t border-slate-200/60 font-mono">
                        <span>Porsi Likuiditas:</span>
                        <span class="text-slate-900 font-bold">{{ round(($acc->current_balance / max(1, $totalBalance)) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400 font-medium">
                <span>{{ $acc->transactions_count }} transaksi</span>
                <span class="text-emerald-700 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terhubung
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MODAL 1: ADD / EDIT ACCOUNT (CLEAN NEAT COMPACT LAYOUT)    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <template x-teleport="body">
        <div x-data="{ 
                open: @entangle('isModalOpen'),
                formatNominal(val) {
                    let num = (val || '').toString().replace(/\D/g, '');
                    return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                }
            }" 
            x-show="open" 
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-[60] overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4" 
            x-cloak>
            
            <div @click.outside="$wire.set('isModalOpen', false)" 
                class="relative w-full max-w-lg bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col animate-in slide-in-from-bottom-6 sm:slide-in-from-bottom-2 duration-200">
            
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>
            
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#C6F24D] text-slate-950 flex items-center justify-center font-bold shadow-2xs">
                        <x-icon name="credit-card" class="w-4 h-4" strokeWidth="2.5" />
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-950">
                            {{ $accountId ? 'Edit Rekening / Dompet' : 'Tambah Rekening / Dompet' }}
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium">Pilih bank / e-wallet resmi atau input manual</p>
                    </div>
                </div>
                <button wire:click="$set('isModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <form wire:submit.prevent="saveAccount" class="p-5 pb-8 sm:pb-5 space-y-4 overflow-y-auto">
                
                <!-- 1. COMPACT PRESET SELECTOR (GRID 6 COLS ON DESKTOP, 4 ON MOBILE) -->
                @if(!$accountId)
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">Pilih Cepat Bank & E-Wallet:</label>
                        <span class="text-[9px] text-slate-400">Klik untuk auto-fill</span>
                    </div>
                    
                    <!-- Presets Grid Container with Safe Style -->
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-1.5 p-2 bg-slate-50 border border-slate-100 rounded-2xl" 
                         style="display: grid; grid-template-columns: repeat(auto-fill, minmax(64px, 1fr)); gap: 6px;">
                        
                        <!-- BCA -->
                        <button type="button" wire:click="selectPreset('BCA', 'bank', '#003B70')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="BCA" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">BCA</span>
                        </button>

                        <!-- Mandiri -->
                        <button type="button" wire:click="selectPreset('Bank Mandiri', 'bank', '#002D62')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="Mandiri" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">Mandiri</span>
                        </button>

                        <!-- BRI -->
                        <button type="button" wire:click="selectPreset('BRI', 'bank', '#00529C')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="BRI" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">BRI</span>
                        </button>

                        <!-- BNI -->
                        <button type="button" wire:click="selectPreset('BNI', 'bank', '#005E6A')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="BNI" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">BNI</span>
                        </button>

                        <!-- Bank Jago -->
                        <button type="button" wire:click="selectPreset('Bank Jago', 'bank', '#8235F4')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="Jago" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">Jago</span>
                        </button>

                        <!-- GoPay -->
                        <button type="button" wire:click="selectPreset('GoPay', 'ewallet', '#00AA13')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="GoPay" type="ewallet" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">GoPay</span>
                        </button>

                        <!-- OVO -->
                        <button type="button" wire:click="selectPreset('OVO', 'ewallet', '#4C3494')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="OVO" type="ewallet" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">OVO</span>
                        </button>

                        <!-- DANA -->
                        <button type="button" wire:click="selectPreset('DANA', 'ewallet', '#118EEA')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="DANA" type="ewallet" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">DANA</span>
                        </button>

                        <!-- ShopeePay -->
                        <button type="button" wire:click="selectPreset('ShopeePay', 'ewallet', '#EE4D2D')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="ShopeePay" type="ewallet" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">SPay</span>
                        </button>

                        <!-- SeaBank -->
                        <button type="button" wire:click="selectPreset('SeaBank', 'bank', '#F26422')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="SeaBank" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">SeaBank</span>
                        </button>

                        <!-- Jenius -->
                        <button type="button" wire:click="selectPreset('Jenius BTPN', 'bank', '#00A3E0')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="Jenius" type="bank" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">Jenius</span>
                        </button>

                        <!-- Cash Tunai -->
                        <button type="button" wire:click="selectPreset('Dompet Tunai', 'cash', '#F59E0B')" 
                            class="py-1.5 px-1 rounded-xl border border-slate-200/90 hover:border-slate-950 hover:bg-white bg-white/80 flex flex-col items-center justify-center gap-1 text-center transition-all cursor-pointer group shadow-2xs">
                            <x-account-logo name="Cash" type="cash" class="w-7 h-7 rounded-lg group-hover:scale-105" />
                            <span class="text-[9px] font-extrabold text-slate-800 truncate w-full text-center">Cash</span>
                        </button>
                    </div>
                </div>
                @endif

                <!-- 2. LIVE REAL-TIME LOGO & CARD PREVIEW -->
                <div class="p-3 bg-slate-50 border border-slate-200/90 rounded-2xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <x-account-logo :name="$name ?: 'BCA'" :type="$type" class="w-9 h-9 rounded-xl shrink-0 shadow-xs" />
                        <div class="min-w-0">
                            <span class="text-xs font-black text-slate-950 block truncate">{{ $name ?: 'Nama Akun / Provider' }}</span>
                            <span class="text-[10px] font-mono text-slate-400 capitalize block">{{ $type }} {{ $account_number ? '• ' . $account_number : '' }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-black uppercase tracking-wider bg-white text-slate-900 border border-slate-200 shadow-2xs shrink-0">
                        Auto Logo
                    </span>
                </div>

                <!-- 3. INPUT FIELDS (CLEAN & SPACIOUS) -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Akun *</label>
                        <input type="text" wire:model.live.debounce.150ms="name" placeholder="e.g. BCA Utama / GoPay / ShopeePay" 
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Akun *</label>
                        <select wire:model.live="type" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 cursor-pointer">
                            <option value="bank">Bank</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="cash">Cash Dompet</option>
                            <option value="investment">Investasi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening / No. HP E-Wallet (Opsional)</label>
                        <input type="text" wire:model.live.debounce.150ms="account_number" placeholder="e.g. 8210984123 atau 081234567890" 
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">{{ $accountId ? 'Saldo Akun Saat Ini (Rp) *' : 'Saldo Awal (Rp) *' }}</label>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.defer="initial_balance" 
                               x-on:input="$event.target.value = formatNominal($event.target.value)"
                               placeholder="0" 
                               class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-base font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        @error('initial_balance') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Footer CTA -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 shrink-0">
                    <div>
                        @if($accountId)
                        <button type="button" 
                                wire:click="deleteAccount({{ $accountId }})" 
                                wire:confirm="Yakin ingin menghapus rekening ini?"
                                class="text-xs font-bold text-rose-600 hover:text-rose-800 transition-colors cursor-pointer">
                            Hapus Akun
                        </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900 cursor-pointer">Batal</button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="saveAccount"
                                class="px-5 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-black shadow-sm active-tap cursor-pointer hover:bg-slate-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="saveAccount">{{ $accountId ? 'Simpan Perubahan' : 'Simpan Akun' }}</span>
                            <span wire:loading wire:target="saveAccount">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </template>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MODAL 2: TRANSFER ANTAR REKENING                           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <template x-teleport="body">
        <div x-data="{ 
                open: @entangle('isTransferModalOpen'),
                formatNominal(val) {
                    let num = (val || '').toString().replace(/\D/g, '');
                    return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                }
            }" 
            x-show="open" 
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-[60] overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
            <div @click.outside="$wire.set('isTransferModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col animate-in slide-in-from-bottom-6 sm:slide-in-from-bottom-2 duration-200">
                <!-- Drag indicator (mobile only) -->
                <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#C6F24D] text-slate-950 flex items-center justify-center font-bold shadow-2xs">
                            <x-icon name="arrow-right-left" class="w-4 h-4" strokeWidth="2.5" />
                        </div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Transfer Antar Rekening</h3>
                    </div>
                    <button wire:click="$set('isTransferModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer"><x-icon name="x" class="w-4 h-4" /></button>
                </div>

                <form wire:submit.prevent="executeTransfer" class="p-5 pb-8 sm:pb-5 space-y-3.5 overflow-y-auto">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Dari Rekening Sumber *</label>
                        <select wire:model.defer="from_account_id" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 cursor-pointer">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ke Rekening Tujuan *</label>
                        <select wire:model.defer="to_account_id" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 cursor-pointer">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Transfer (Rp) *</label>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.defer="transfer_amount" 
                               x-on:input="$event.target.value = formatNominal($event.target.value)"
                               placeholder="500.000" 
                               class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-base font-bold font-mono text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        @error('transfer_amount') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" wire:model.defer="transfer_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                            @error('transfer_date') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                            <input type="text" wire:model.defer="transfer_note" placeholder="Top up / Pindah saldo" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                        <button type="button" wire:click="$set('isTransferModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900 cursor-pointer">Batal</button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="executeTransfer"
                                class="px-5 py-2.5 rounded-xl bg-[#C6F24D] text-slate-950 text-xs font-black shadow-sm active-tap cursor-pointer hover:bg-[#b8e640] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="executeTransfer">Kirim Transfer</span>
                            <span wire:loading wire:target="executeTransfer">Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
