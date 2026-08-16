<div class="space-y-6">
    
    <!-- MULTI-WISHLIST COMBINED PLANNING SUMMARY (Clean White Banner) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="p-1.5 rounded-xl bg-[#C6F24D] text-slate-950">
                        <x-icon name="sparkles" class="w-4 h-4" />
                    </span>
                    <h3 class="text-base font-extrabold tracking-tight">Rencana Multi-Wishlist Gabungan</h3>
                </div>
                <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                    Berdasarkan rata-rata surplus cashflow Anda (± <strong class="text-[#C6F24D] font-mono">Rp {{ number_format($multiPlan['avg_monthly_capacity'], 0, ',', '.') }}/bulan</strong>), menyelesaikan seluruh <strong class="text-white">{{ $multiPlan['total_active_items'] }} wishlist aktif</strong> membutuhkan estimasi waktu <strong class="text-[#C6F24D] font-mono">± {{ $multiPlan['estimated_completion_months'] }} bulan</strong>.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3 shrink-0 text-center">
                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-3 rounded-2xl">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Total Nilai</span>
                    <span class="text-xs sm:text-sm font-extrabold font-mono text-white">Rp {{ number_format($multiPlan['total_target_value'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-3 rounded-2xl">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Terkumpul</span>
                    <span class="text-xs sm:text-sm font-extrabold font-mono text-[#C6F24D]">Rp {{ number_format($multiPlan['total_saved_value'], 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-3 rounded-2xl">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Kurang</span>
                    <span class="text-xs sm:text-sm font-extrabold font-mono text-rose-300">Rp {{ number_format($multiPlan['total_shortage'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR & ACTION -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5 flex-1">
            <!-- Search -->
            <div class="relative flex-1 sm:flex-initial min-w-[180px]">
                <span class="absolute left-3.5 top-2.5 text-slate-400"><x-icon name="search" class="w-3.5 h-3.5" /></span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari wishlist..." 
                       class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl pl-9 pr-3 py-1.5 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-slate-950">
            </div>

            <!-- Priority Filter -->
            <select wire:model.live="filterPriority" 
                    class="bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3 py-1.5 text-xs font-bold text-slate-700">
                <option value="all">Semua Prioritas</option>
                <option value="critical">🔴 Critical</option>
                <option value="high">🟠 High</option>
                <option value="medium">🟡 Medium</option>
                <option value="low">⚪ Low</option>
            </select>

            <!-- Status Filter -->
            <select wire:model.live="filterStatus" 
                    class="bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3 py-1.5 text-xs font-bold text-slate-700">
                <option value="active">Aktif (Saving/Plan)</option>
                <option value="purchased">Sudah Dibeli</option>
                <option value="all">Semua</option>
            </select>
        </div>

        <button wire:click="openCreateModal" 
                class="px-4 py-2 rounded-2xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-extrabold shadow-sm active-tap transition-all flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
            <span class="w-4 h-4 rounded-full bg-[#C6F24D] text-slate-950 flex items-center justify-center text-[10px] font-black">+</span>
            <span>Tambah Wishlist</span>
        </button>
    </div>

    <!-- WISHLIST CARDS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($wishlists as $item)
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition-all group">
            <div>
                <!-- Top Tags -->
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-[9px] font-extrabold uppercase px-2.5 py-0.5 rounded-full {{ $item->priority === 'critical' ? 'bg-rose-100 text-rose-700' : ($item->priority === 'high' ? 'bg-orange-100 text-orange-700' : ($item->priority === 'medium' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')) }}">
                        {{ ucfirst($item->priority) }}
                    </span>
                    <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold">
                        {{ $item->category }}
                    </span>
                </div>

                <!-- Item Title & External Link -->
                <div class="flex items-start justify-between gap-2">
                    <h4 class="font-bold text-base text-slate-900 group-hover:text-indigo-600 transition-colors">
                        {{ $item->name }}
                    </h4>
                    @if($item->product_url)
                    <a href="{{ $item->product_url }}" target="_blank" class="text-slate-400 hover:text-slate-900 p-1 shrink-0" title="Buka Link Produk">
                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                    </a>
                    @endif
                </div>

                @if($item->notes)
                <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $item->notes }}</p>
                @endif

                <!-- Pricing & Tracking Info -->
                <div class="mt-4 p-3 bg-[#F8F9FA] rounded-2xl border border-slate-100 space-y-1">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="text-slate-500 font-sans">Harga Sekarang:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-slate-950 text-sm">Rp {{ number_format($item->current_price, 0, ',', '.') }}</span>
                            <button wire:click="openPriceModal({{ $item->id }})" class="text-[10px] text-slate-900 hover:underline font-bold font-sans" title="Update Harga Manual">
                                (Track)
                            </button>
                        </div>
                    </div>
                    @if($item->target_price != $item->current_price)
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                        <span>Target Awal:</span>
                        <span class="line-through">Rp {{ number_format($item->target_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                    <!-- Saving Progress Bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs mb-1.5 font-mono font-bold">
                            <span class="text-slate-600">Terkumpul: <strong class="text-slate-950">Rp {{ number_format($item->saved_amount, 0, ',', '.') }}</strong></span>
                            <span class="text-emerald-700 font-black">{{ $item->progress_percentage }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/60">
                            <div class="h-full bg-gradient-to-r from-emerald-500 via-lime-500 to-[#C6F24D] rounded-full transition-all duration-500 shadow-xs" style="width: {{ $item->progress_percentage }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5 font-mono">
                            <span>Kurang: <strong class="text-rose-500 font-bold">Rp {{ number_format($item->shortage_amount, 0, ',', '.') }}</strong></span>
                            <span>Target: {{ $item->target_date ? \Carbon\Carbon::parse($item->target_date)->translatedFormat('M Y') : '-' }}</span>
                        </div>
                    </div>

                <!-- Saving Plan Feasibility Status Badge -->
                @if(isset($item->plan_eval))
                <div class="mt-4 p-3 rounded-2xl border {{ $item->plan_eval['status'] === 'realistic' ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : ($item->plan_eval['status'] === 'at_risk' ? 'bg-amber-50/80 border-amber-200 text-amber-900' : 'bg-rose-50/80 border-rose-200 text-rose-900') }} text-xs">
                    <div class="flex items-center justify-between font-bold mb-0.5 text-[11px]">
                        <span>Plan: {{ $item->plan_eval['label'] }}</span>
                        @if($item->shortage_amount > 0)
                        <span class="font-mono">± Rp {{ number_format($item->monthly_saving_need, 0, ',', '.') }}/bln</span>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-600 leading-relaxed">{{ $item->plan_eval['note'] }}</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons Footer -->
            <div class="mt-5 pt-3.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                    @if($item->status !== 'purchased')
                    {{-- Quick saving: opens global QuickTransactionModal pre-filled --}}
                    <button wire:click="openQuickSavingModal({{ $item->id }})"
                            class="px-3 py-1.5 rounded-xl bg-[#C6F24D] text-slate-950 text-xs font-bold active-tap transition-all shadow-sm flex items-center gap-1 cursor-pointer"
                            title="Catat pengeluaran saving ke modal transaksi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.8 0 3 2 4.5V20h4v-2h3v2h4v-4c1-.5 1.7-1 2-2h2v-4h-2c0-1-.5-1.5-1-2V5z"/><path d="M2 9v1a2 2 0 0 0 2 2h1"/><path d="M16 11h.01"/></svg>
                        <span>Catat Saving</span>
                    </button>
                    {{-- Internal allocation: opens inline modal --}}
                    <button wire:click="openSavingModal({{ $item->id }})"
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold active-tap transition-all flex items-center gap-1 cursor-pointer"
                            title="Alokasi internal dana tabungan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Setor Dana</span>
                    </button>
                    @endif

                    <a href="{{ route('purchase-planning', ['wishlist_id' => $item->id]) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold transition-colors flex items-center gap-1" title="Simulasi Kelayakan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="16" y1="12" x2="8" y2="12"/><line x1="8" y1="18" x2="11" y2="18"/></svg>
                        <span>Simulasi</span>
                    </a>
                </div>

                <div class="flex items-center gap-1">
                    @if($item->status !== 'purchased')
                    <button wire:click="markAsPurchased({{ $item->id }})" wire:confirm="Tandai item ini sudah dibeli?" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg transition-colors" title="Tandai Sudah Dibeli">
                        <x-icon name="check" class="w-4 h-4" />
                    </button>
                    @endif
                    <button wire:click="openEditModal({{ $item->id }})" class="p-1.5 text-slate-400 hover:text-slate-900 rounded-lg transition-colors" title="Edit Wishlist">
                        <x-icon name="edit" class="w-4 h-4" />
                    </button>
                    <button wire:click="deleteWishlist({{ $item->id }})" wire:confirm="Hapus wishlist ini?" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" title="Hapus Wishlist">
                        <x-icon name="trash-2" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-slate-200/70 shadow-sm">
            <x-icon name="shopping-bag" class="w-10 h-10 text-slate-300 mx-auto mb-2" />
            <h3 class="text-base font-bold text-slate-900">Belum ada item wishlist</h3>
            <p class="text-xs text-slate-400 mt-1">Mulai rencanakan pembelian peralatan kerja impian Anda.</p>
            <button wire:click="openCreateModal" class="mt-3 px-4 py-2 rounded-2xl bg-slate-950 text-white text-xs font-bold">
                + Buat Wishlist Baru
            </button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $wishlists->links() }}
    </div>

    <!-- MODAL 1: ADD / EDIT WISHLIST -->
    <div x-data="{ open: @entangle('isFormModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isFormModalOpen', false)" class="relative w-full max-w-lg bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-base font-extrabold text-slate-900">{{ $wishlistId ? 'Edit Item Wishlist' : 'Tambah Wishlist Baru' }}</h3>
                <button wire:click="$set('isFormModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-1"><x-icon name="x" class="w-5 h-5" /></button>
            </div>
            <form wire:submit.prevent="saveWishlist" class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang *</label>
                    <input type="text" wire:model.defer="name" placeholder="e.g. DJI Pocket 4 Creator Combo" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                        <select wire:model.defer="category" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                            <option value="Alat Kerja">Alat Kerja</option>
                            <option value="Gadget">Gadget & Monitor</option>
                            <option value="Hobi">Hobi</option>
                            <option value="Kendaraan">Kendaraan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Prioritas *</label>
                        <select wire:model.defer="priority" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                            <option value="critical">🔴 Critical</option>
                            <option value="high">🟠 High</option>
                            <option value="medium">🟡 Medium</option>
                            <option value="low">⚪ Low</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Harga (Rp) *</label>
                        <input type="number" wire:model.defer="target_price" placeholder="8000000" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Saat Ini (Rp) *</label>
                        <input type="number" wire:model.defer="current_price" placeholder="8000000" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Tanggal Beli</label>
                        <input type="date" wire:model.defer="target_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Link Marketplace</label>
                        <input type="url" wire:model.defer="product_url" placeholder="https://..." class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                    <textarea wire:model.defer="notes" rows="2" placeholder="Catatan kegunaan..." class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isFormModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-extrabold">Simpan Wishlist</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: MANUAL PRICE TRACKING -->
    <div x-data="{ open: @entangle('isPriceModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isPriceModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-base font-extrabold text-slate-900">Update Harga (Price Tracking)</h3>
                <button wire:click="$set('isPriceModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-1"><x-icon name="x" class="w-5 h-5" /></button>
            </div>
            <form wire:submit.prevent="recordPriceUpdate" class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Harga Terbaru (Rp) *</label>
                    <input type="number" wire:model.defer="new_price" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-base font-bold font-mono text-slate-900 focus:ring-2 focus:ring-slate-950">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                    <input type="text" wire:model.defer="price_note" placeholder="Diskon tanggal kembar / flash sale" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                </div>

                @if(count($priceHistories) > 0)
                <div class="pt-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Riwayat Harga:</label>
                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                        @foreach($priceHistories as $ph)
                        <div class="flex items-center justify-between text-xs p-2.5 bg-[#F8F9FA] rounded-xl border border-slate-100 font-mono">
                            <span class="text-slate-500 text-[11px]">{{ \Carbon\Carbon::parse($ph->recorded_at)->translatedFormat('d M Y') }}</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($ph->price, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isPriceModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Tutup</button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-slate-950 text-white text-xs font-extrabold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: SAVING ALLOCATION -->
    <div x-data="{ open: @entangle('isSavingModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isSavingModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-base font-extrabold text-slate-900">Setor Tabungan Wishlist</h3>
                <button wire:click="$set('isSavingModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-1"><x-icon name="x" class="w-5 h-5" /></button>
            </div>
            <form wire:submit.prevent="allocateSaving" class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Setoran (Rp) *</label>
                    <input type="number" wire:model.defer="saving_amount" placeholder="1000000" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-base font-bold font-mono text-slate-900 focus:ring-2 focus:ring-slate-950">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dari Rekening / Wallet (Opsional)</label>
                    <select wire:model.defer="savingAccountId" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                        <option value="">Alokasi Virtual</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" wire:model.defer="saving_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                        <input type="text" wire:model.defer="saving_note" placeholder="Profit project" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isSavingModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-2xl bg-[#C6F24D] text-slate-950 text-xs font-extrabold shadow-sm active-tap">Setor Dana</button>
                </div>
            </form>
        </div>
    </div>

</div>
