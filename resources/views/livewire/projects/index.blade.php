<div class="space-y-6">
    
    <!-- TOP BUSINESS PROFITABILITY KPI STRIP (Clean & Compact) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Revenue</span>
            <div class="text-base sm:text-xl font-black font-mono text-slate-950">
                Rp {{ number_format($totalProjectRevenue, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 font-medium block">Nilai seluruh kontrak</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Biaya Operasional</span>
            <div class="text-base sm:text-xl font-black font-mono text-slate-950">
                Rp {{ number_format($totalProjectCosts, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 font-medium block">Sewa alat & tim</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Profit Real</span>
            <div class="text-base sm:text-xl font-black font-mono text-emerald-600">
                Rp {{ number_format($totalProjectProfit, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 font-medium block">Revenue − Biaya</span>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Rata-rata Margin</span>
            <div class="text-base sm:text-xl font-black font-mono text-slate-950">
                {{ $avgMargin }}%
            </div>
            <span class="text-[10px] text-slate-400 font-medium block">Profitabilitas bisnis</span>
        </div>
    </div>

    <!-- CONTROLS & FILTER BAR -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-2.5 text-slate-400"><x-icon name="search" class="w-4 h-4" /></span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama project..." 
                    class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl pl-9.5 pr-3 py-2 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-900 transition-colors">
            </div>
            
            <select wire:model.live="filterStatus" 
                class="bg-[#F8F9FA] border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:border-slate-900 shrink-0">
                <option value="all">Semua Status</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="prospect">Prospect</option>
            </select>
        </div>

        <button wire:click="openCreateProjectModal" 
            class="px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-extrabold shadow-sm active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Project Baru</span>
        </button>
    </div>

    <!-- PROJECTS LIST -->
    <div class="space-y-4">
        @forelse($projects as $proj)
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 sm:p-6 shadow-sm space-y-4 hover:border-slate-300 transition-all">
            
            <!-- Main Header Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ $proj->name }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $proj->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : ($proj->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                            {{ str_replace('_', ' ', $proj->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Klien: <strong class="text-slate-800">{{ $proj->client->name ?? '-' }}</strong> &bull; {{ ucwords(str_replace('_', ' ', $proj->category)) }}
                    </p>
                </div>

                <!-- Financial Stats Badges -->
                <div class="grid grid-cols-3 gap-2 bg-[#F8F9FA] p-3 rounded-2xl border border-slate-100 text-center sm:text-right shrink-0">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Revenue</span>
                        <span class="text-xs sm:text-sm font-bold font-mono text-slate-900 block">Rp {{ number_format($proj->total_revenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-l border-slate-200 pl-2">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Profit</span>
                        <span class="text-xs sm:text-sm font-black font-mono text-emerald-600 block">Rp {{ number_format($proj->profit, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-l border-slate-200 pl-2">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Margin</span>
                        <span class="text-xs sm:text-sm font-black font-mono text-slate-950 block">{{ $proj->margin_percentage }}%</span>
                    </div>
                </div>
            </div>

            <!-- Invoices & Cost Items Sub-bar -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-3 font-mono text-[11px]">
                    <span class="text-slate-500">Lunas: <strong class="text-emerald-600">Rp {{ number_format($proj->paid_invoices_total, 0, ',', '.') }}</strong></span>
                    @if($proj->outstanding_invoices_total > 0)
                    <span class="text-slate-500">Piutang: <strong class="text-amber-600">Rp {{ number_format($proj->outstanding_invoices_total, 0, ',', '.') }}</strong></span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button wire:click="openAddCostModal({{ $proj->id }})" 
                        class="flex-1 sm:flex-initial px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <x-icon name="plus" class="w-3.5 h-3.5 text-slate-600" />
                        <span>Catat Biaya</span>
                    </button>
                    <button wire:click="openAddInvoiceModal({{ $proj->id }})" 
                        class="flex-1 sm:flex-initial px-3.5 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <x-icon name="file-text" class="w-3.5 h-3.5" />
                        <span>Buat Invoice</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="py-12 text-center bg-white rounded-3xl border border-slate-200/80 text-slate-400 text-xs shadow-sm">
            Tidak ada project ditemukan.
        </div>
        @endforelse
    </div>

    <!-- MODAL 1: ADD PROJECT -->
    <div x-data="{ open: @entangle('isProjectModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isProjectModalOpen', false)" class="relative w-full max-w-lg bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-base font-extrabold text-slate-900">Project Freelance Baru</h3>
                <button wire:click="$set('isProjectModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer"><x-icon name="x" class="w-5 h-5" /></button>
            </div>
            <form wire:submit.prevent="saveProject" class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Project *</label>
                    <input type="text" wire:model.defer="name" placeholder="e.g. Multi-Cam Livestreaming Muswil" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Klien *</label>
                            <a href="{{ route('clients') }}" class="text-[10px] font-bold text-teal-700 hover:text-teal-900 hover:underline flex items-center gap-0.5">
                                <span>+ Tambah</span>
                            </a>
                        </div>
                        <select wire:model.live="client_id" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900 cursor-pointer">
                            <option value="">-- Pilih Klien --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company ?? 'Individu' }})</option>
                            @endforeach
                            <option value="new_client" class="font-bold text-teal-700 bg-teal-50">
                                ➕ + Tambah Klien Baru &rarr;
                            </option>
                        </select>
                        @if($clients->isEmpty())
                            <div class="mt-1.5 p-2 rounded-xl bg-amber-50 border border-amber-200 text-[10px] text-amber-900 flex items-center justify-between gap-1">
                                <span>Belum ada klien.</span>
                                <a href="{{ route('clients') }}" class="font-bold underline text-amber-950 hover:text-black">Tambah &rarr;</a>
                            </div>
                        @endif
                        @error('client_id') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Layanan *</label>
                        <select wire:model.defer="category" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                            <option value="photo_video">Fotografi / Videografi</option>
                            <option value="livestreaming">Livestreaming / Broadcast</option>
                            <option value="design_branding">Desain & Branding</option>
                            <option value="web_dev">Web & Software Dev</option>
                            <option value="social_media">Social Media Management</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3"
                     x-data="{
                         formatNominal(val) {
                             if (!val) return '';
                             let clean = String(val).replace(/\D/g, '');
                             if (!clean) return '';
                             return new Intl.NumberFormat('id-ID').format(clean);
                         }
                     }">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Revenue (Nilai Kontrak) *</label>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.defer="total_revenue" 
                               x-on:input="$el.value = formatNominal($el.value)"
                               placeholder="0" 
                               class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-900">
                        @error('total_revenue') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Biaya Operasional</label>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.defer="estimated_cost" 
                               x-on:input="$el.value = formatNominal($el.value)"
                               placeholder="0" 
                               class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" wire:model.defer="start_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deadline / Selesai</label>
                        <input type="date" wire:model.defer="deadline" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Awal</label>
                    <select wire:model.defer="status" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                        <option value="prospect">Prospect (Penawaran / Proposal)</option>
                        <option value="in_progress">In Progress (Sedang Dikerjakan)</option>
                        <option value="completed">Completed (Selesai)</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" wire:click="$set('isProjectModalOpen', false)" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 cursor-pointer shadow-sm">Simpan Project</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ADD COST TO PROJECT -->
    <div x-data="{ 
            open: @entangle('isCostModalOpen'),
            formatNominal(val) {
                if (!val) return '';
                let clean = String(val).replace(/\D/g, '');
                if (!clean) return '';
                return new Intl.NumberFormat('id-ID').format(clean);
            }
         }" 
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isCostModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900">Catat Biaya Project</h3>
                <button wire:click="$set('isCostModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer"><x-icon name="x" class="w-5 h-5" /></button>
            </div>
            <form wire:submit.prevent="saveCost" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Pengeluaran *</label>
                    <input type="text" wire:model.defer="cost_description" placeholder="e.g. Sewa Lensa Sony GM 70-200" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                    @error('cost_description') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp) *</label>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.defer="cost_amount" 
                               x-on:input="$el.value = formatNominal($el.value)"
                               placeholder="0" 
                               class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-900">
                        @error('cost_amount') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" wire:model.defer="cost_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Pengeluaran</label>
                    <select wire:model.defer="cost_category_id" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" wire:click="$set('isCostModalOpen', false)" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 cursor-pointer shadow-sm">Simpan Biaya</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: ADD INVOICE TO PROJECT -->
    <div x-data="{ 
            open: @entangle('isInvoiceModalOpen'),
            formatNominal(val) {
                if (!val) return '';
                let clean = String(val).replace(/\D/g, '');
                if (!clean) return '';
                return new Intl.NumberFormat('id-ID').format(clean);
            }
         }" 
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isInvoiceModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[32px] sm:rounded-3xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900">Terbitkan Invoice Penagihan</h3>
                <button wire:click="$set('isInvoiceModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer"><x-icon name="x" class="w-5 h-5" /></button>
            </div>
            <form wire:submit.prevent="saveInvoice" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Invoice *</label>
                        <input type="text" wire:model.defer="invoice_number" placeholder="INV/2026/001" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-900">
                        @error('invoice_number') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Tagihan *</label>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.defer="invoice_amount" 
                               x-on:input="$el.value = formatNominal($el.value)"
                               placeholder="0" 
                               class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-900">
                        @error('invoice_amount') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jatuh Tempo (Due Date) *</label>
                        <input type="date" wire:model.defer="due_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                        @error('due_date') <span class="text-xs text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Pembayaran</label>
                        <select wire:model.defer="invoice_status" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                            <option value="sent">Terkirim (Menunggu Bayar)</option>
                            <option value="paid">Lunas (Sudah Diterima)</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" wire:click="$set('isInvoiceModalOpen', false)" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 cursor-pointer shadow-sm">Simpan Invoice</button>
                </div>
            </form>
        </div>
    </div>

</div>
