<div class="space-y-4 sm:space-y-6">
    
    <!-- TOP SUMMARY & ACTIONS -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
        <div>
            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Likuiditas Seluruh Akun</span>
            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 mt-0.5">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>
            <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Tersimpan di {{ $accounts->count() }} akun aktif</p>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
            <button wire:click="openTransferModal" 
                    type="button"
                    class="w-full sm:w-auto px-3.5 py-2.5 rounded-xl sm:rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs active-tap">
                <x-icon name="arrow-right-left" class="w-3.5 h-3.5 text-slate-700" />
                <span>Transfer</span>
            </button>
            <button wire:click="openCreateModal" 
                    type="button"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl sm:rounded-2xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-extrabold shadow-2xs active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <span class="w-4 h-4 rounded-full bg-[#C6F24D] text-slate-950 flex items-center justify-center text-[10px] font-black">+</span>
                <span>Tambah Akun</span>
            </button>
        </div>
    </div>

    <!-- ACCOUNT CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">
        @foreach($accounts as $acc)
        <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:shadow-md transition-all group">
            <div>
                <!-- Top Header -->
                <div class="flex items-center justify-between mb-3.5">
                    <div class="flex items-center gap-3">
                        <x-account-logo :name="$acc->name" :type="$acc->type" class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl shrink-0" />
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-sm sm:text-base text-slate-900 group-hover:text-teal-600 transition-colors truncate">{{ $acc->name }}</h4>
                            <span class="text-[10px] sm:text-[11px] text-slate-400 capitalize truncate block">{{ $acc->type }} {{ $acc->account_number ? '• ' . $acc->account_number : '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Balance Display -->
                <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold text-slate-400 block">Saldo Akun:</span>
                    <span class="text-lg sm:text-xl font-extrabold font-mono text-slate-900 block mt-0.5">
                        Rp {{ number_format($acc->current_balance, 0, ',', '.') }}
                    </span>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2 pt-2 border-t border-slate-200/60 font-mono">
                        <span>Porsi Likuiditas:</span>
                        <span class="text-slate-900 font-bold">{{ round(($acc->current_balance / max(1, $totalBalance)) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] sm:text-[11px] text-slate-400">
                <span>{{ $acc->transactions_count }} transaksi</span>
                <span class="text-emerald-700 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MODAL 1: ADD ACCOUNT -->
    <div x-data="{ open: @entangle('isModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>
            
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Tambah Rekening / Dompet</h3>
                <button wire:click="$set('isModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors"><x-icon name="x" class="w-4 h-4" /></button>
            </div>
            <form wire:submit.prevent="saveAccount" class="p-5 space-y-3.5 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Akun *</label>
                    <input type="text" wire:model.defer="name" placeholder="e.g. BCA Bisnis / GoPay / Dompet" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Akun *</label>
                        <select wire:model.defer="type" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                            <option value="bank">Bank</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="cash">Cash Dompet</option>
                            <option value="investment">Investasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Warna Label</label>
                        <input type="color" wire:model.defer="color" class="w-full h-9 bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl p-1 cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening / HP (Opsional)</label>
                    <input type="text" wire:model.defer="account_number" placeholder="8210..." class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Saldo Awal (Rp) *</label>
                    <input type="number" wire:model.defer="initial_balance" placeholder="0" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-slate-950 text-white text-xs font-extrabold shadow-sm active-tap">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: TRANSFER -->
    <div x-data="{ open: @entangle('isTransferModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isTransferModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Transfer Antar Rekening</h3>
                <button wire:click="$set('isTransferModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors"><x-icon name="x" class="w-4 h-4" /></button>
            </div>
            <form wire:submit.prevent="executeTransfer" class="p-5 space-y-3.5 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dari Rekening Sumber *</label>
                    <select wire:model.defer="from_account_id" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ke Rekening Tujuan *</label>
                    <select wire:model.defer="to_account_id" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Transfer (Rp) *</label>
                    <input type="number" wire:model.defer="transfer_amount" placeholder="500000" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-base font-bold font-mono text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" wire:model.defer="transfer_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                        <input type="text" wire:model.defer="transfer_note" placeholder="Top up / Pindah saldo" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isTransferModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-[#C6F24D] text-slate-950 text-xs font-extrabold shadow-sm active-tap">Kirim Transfer</button>
                </div>
            </form>
        </div>
    </div>

</div>
