<div>
    <template x-teleport="body">
        <div x-data="{ 
                open: @entangle('isOpen'),
                formatNominal(val) {
                    let num = (val || '').toString().replace(/\D/g, '');
                    return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                }
             }" 
             x-show="open" 
             x-transition.opacity.duration.200ms
             class="fixed inset-0 z-[70] overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4" 
             x-cloak>
            
            <div @click.outside="$wire.set('isOpen', false)" class="relative w-full max-w-lg bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col animate-in slide-in-from-bottom-6 sm:slide-in-from-bottom-2 duration-200">
                
                <!-- Drag Indicator -->
                <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center font-bold shadow-sm">
                            <x-icon name="users" class="w-5 h-5 text-slate-950" strokeWidth="2.5" />
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950">Bagi Tagihan & Patungan (Split Bill)</h3>
                            <p class="text-xs text-slate-400 font-medium">Bagi rata pengeluaran makan, nongkrong, atau kos</p>
                        </div>
                    </div>
                    <button wire:click="$set('isOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Body Form -->
                <div class="p-6 space-y-4 overflow-y-auto pb-8 sm:pb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Acara / Patungan</label>
                        <input type="text" wire:model.defer="billName" placeholder="e.g. Makan Malam Pecel Lele / Sewa Lapangan Futsal" 
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Total Tagihan (Rp) *</label>
                            <input type="text" inputmode="numeric" wire:model.live.debounce.300ms="totalAmount" 
                                x-on:input="$event.target.value = formatNominal($event.target.value)"
                                placeholder="120.000" 
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-base font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Orang</label>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="$set('totalPeople', Math.max(2, $wire.totalPeople - 1))" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 font-black text-slate-900 flex items-center justify-center cursor-pointer active:scale-95">-</button>
                                <input type="number" wire:model.live="totalPeople" min="2" max="50" class="flex-1 text-center bg-[#F8F9FA] border border-slate-200 rounded-xl py-2 font-mono font-bold text-sm text-slate-900">
                                <button type="button" wire:click="$set('totalPeople', Math.min(50, $wire.totalPeople + 1))" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 font-black text-slate-900 flex items-center justify-center cursor-pointer active:scale-95">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Highlight Card Result -->
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-emerald-800 block">Bagian per Orang:</span>
                            <span class="text-xl sm:text-2xl font-black font-mono text-emerald-950 block mt-0.5">
                                Rp {{ number_format($this->perPersonAmount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-medium text-emerald-700 block">Total {{ $totalPeople }} Orang</span>
                            <span class="text-[11px] font-bold text-emerald-900">Rata & Adil ✨</span>
                        </div>
                    </div>

                    <!-- Member Checklist -->
                    <div class="space-y-2 pt-1">
                        <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Daftar Anggota & Status Bayar:</label>
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($members as $index => $m)
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200/80 transition-all hover:bg-white hover:border-slate-300">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <button type="button" wire:click="togglePaid({{ $index }})" class="cursor-pointer">
                                        @if($m['is_paid'])
                                            <span class="w-6 h-6 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center text-xs font-black">✓</span>
                                        @else
                                            <span class="w-6 h-6 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center"></span>
                                        @endif
                                    </button>
                                    <input type="text" wire:model.defer="members.{{ $index }}.name" class="bg-transparent text-xs font-bold text-slate-900 focus:outline-none border-b border-transparent focus:border-slate-400 w-36 sm:w-48">
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($m['is_paid'])
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">Lunas</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold">Belum</span>
                                        <a href="https://wa.me/?text={{ $this->getWhatsAppShareText($m['name']) }}" target="_blank" class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold flex items-center gap-1 transition-all shadow-2xs">
                                            <span>WA</span>
                                            <x-icon name="send" class="w-3 h-3" />
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer Close -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" wire:click="$set('isOpen', false)" class="px-5 py-2.5 rounded-2xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 transition-all cursor-pointer shadow-sm">
                            Selesai & Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
