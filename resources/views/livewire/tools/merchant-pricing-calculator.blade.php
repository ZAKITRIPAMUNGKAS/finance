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
                        <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-bold shadow-sm">
                            <x-icon name="shopping-bag" class="w-5 h-5 text-slate-950" strokeWidth="2.5" />
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-950">Kalkulator Margin & Harga Jual</h3>
                            <p class="text-xs text-slate-400 font-medium">Hitung harga jual toko online setelah dipotong admin marketplace</p>
                        </div>
                    </div>
                    <button wire:click="$set('isOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Body Form -->
                <div class="p-6 space-y-4 overflow-y-auto pb-8 sm:pb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk / Barang</label>
                        <input type="text" wire:model.defer="productName" placeholder="e.g. Kemeja Pria / Skincare Serum" 
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Modal Beli / HPP (Rp) *</label>
                            <input type="text" inputmode="numeric" wire:model.live.debounce.300ms="baseCost" 
                                x-on:input="$event.target.value = formatNominal($event.target.value)"
                                placeholder="65.000" 
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-base font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Biaya Packing & Lakban</label>
                            <input type="text" inputmode="numeric" wire:model.live.debounce.300ms="packingCost" 
                                x-on:input="$event.target.value = formatNominal($event.target.value)"
                                placeholder="3.000" 
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-base font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Fee Admin Marketplace (%)</label>
                            <select wire:model.live="marketplaceFeePercent" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3 py-2.5 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 cursor-pointer">
                                <option value="0">0% (Toko Offline / WA)</option>
                                <option value="4.0">4.0% (Shopee Non-Star)</option>
                                <option value="6.5">6.5% (Shopee Star / Star+)</option>
                                <option value="8.0">8.0% (TikTok Shop / Tokopedia)</option>
                                <option value="10.0">10.0% (Mall / Promo Fee)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Margin Laba Bersih (%)</label>
                            <select wire:model.live="targetProfitPercent" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3 py-2.5 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 cursor-pointer">
                                <option value="15">15% (Margin Tipis / Grosir)</option>
                                <option value="20">20% (Standar Retail)</option>
                                <option value="30">30% (Rekomendasi Online Shop)</option>
                                <option value="40">40% (Produk Fashion / Beauty)</option>
                                <option value="50">50% (High Margin / Custom)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Highlight Selling Price Card -->
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200/90 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-amber-800 block">Harga Jual Rekomendasi:</span>
                                <span class="text-2xl sm:text-3xl font-black font-mono text-amber-950 block mt-0.5">
                                    Rp {{ number_format($this->recommendedSellingPrice, 0, ',', '.') }}
                                </span>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-amber-200/80 text-amber-900 text-xs font-black">
                                Margin {{ $targetProfitPercent }}%
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-amber-200/60 text-xs">
                            <div>
                                <span class="text-slate-500 block text-[10px]">Potongan Admin ({{ $marketplaceFeePercent }}%):</span>
                                <span class="font-mono font-bold text-rose-700">Rp {{ number_format($this->adminFeeDeduction, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px]">Laba Bersih per Produk:</span>
                                <span class="font-mono font-extrabold text-emerald-700">Rp {{ number_format($this->netProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Close -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" wire:click="$set('isOpen', false)" class="px-5 py-2.5 rounded-2xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 transition-all cursor-pointer shadow-sm">
                            Tutup Kalkulator
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
