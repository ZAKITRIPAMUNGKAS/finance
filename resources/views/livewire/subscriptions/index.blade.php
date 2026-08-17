<div class="space-y-6 max-w-7xl mx-auto pb-16">
    
    <!-- Toast Message -->
    @if(session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-xs animate-fade-in">
        <div class="flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-xs flex items-center justify-between shadow-xs animate-fade-in">
        <div class="flex items-center gap-2">
            <x-icon name="alert-circle" class="w-4 h-4 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold tracking-wider uppercase bg-[#C6F24D] text-slate-950 border border-slate-900/10">
                    Recurring Engine
                </span>
                <span class="text-xs text-slate-400 font-medium">Auto-Runway Control</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Subscriptions & Burn Rate</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola seluruh langganan software, internet, & tagihan rutin freelancer</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" wire:click="openCreateModal"
                class="px-4 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] font-extrabold text-xs sm:text-sm hover:bg-slate-800 shadow-sm transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                <x-icon name="plus" class="w-4 h-4 text-[#C6F24D]" strokeWidth="2.5" />
                <span>+ Tambah Langganan</span>
            </button>
        </div>
    </div>

    <!-- 3 KPI STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Monthly Burn Rate -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Fixed Monthly Burn</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <x-icon name="flame" class="w-4 h-4 text-rose-600" />
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-slate-950">
                Rp {{ number_format($monthlyBurnRate, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500 font-medium">
                Proyeksi tahunan: <strong class="font-mono text-slate-900">Rp {{ number_format($yearlyBurnRate, 0, ',', '.') }}</strong>
            </p>
        </div>

        <!-- 2. Active Subscriptions Count -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Langganan Aktif</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center">
                    <x-icon name="repeat" class="w-4 h-4 text-slate-900" />
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-slate-950">
                {{ $subscriptions->where('status', 'active')->count() }} <span class="text-base font-normal text-slate-400">Layanan</span>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">
                Total terdaftar: <strong class="text-slate-900">{{ $subscriptions->count() }} layanan</strong>
            </p>
        </div>

        <!-- 3. Due Soon Alerts -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jatuh Tempo (≤ 7 Hari)</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <x-icon name="clock" class="w-4 h-4 text-amber-600" />
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono {{ $dueSoonCount > 0 ? 'text-amber-600' : 'text-slate-950' }}">
                {{ $dueSoonCount }} <span class="text-base font-normal text-slate-400">Tagihan</span>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">
                {{ $dueSoonCount > 0 ? 'Siapkan saldo di rekening terkait' : 'Semua tagihan terkelola aman' }}
            </p>
        </div>
    </div>

    <!-- FILTER TABS & SEARCH -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="$set('filterStatus', 'all')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterStatus === 'all' ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">
                Semua ({{ $subscriptions->count() }})
            </button>
            <button type="button" wire:click="$set('filterStatus', 'active')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterStatus === 'active' ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">
                Aktif ({{ $subscriptions->where('status', 'active')->count() }})
            </button>
            <button type="button" wire:click="$set('filterStatus', 'paused')"
                class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition-all cursor-pointer {{ $filterStatus === 'paused' ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">
                Dijeda ({{ $subscriptions->where('status', 'paused')->count() }})
            </button>
        </div>
    </div>

    <!-- SUBSCRIPTIONS GRID -->
    @if($subscriptions->isEmpty())
    <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
            <x-icon name="repeat" class="w-7 h-7" />
        </div>
        <h3 class="text-base font-extrabold text-slate-900">Belum ada langganan terdaftar</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
            Catat semua biaya rutin bulanan seperti ChatGPT, Adobe, Hosting, atau Internet untuk menghitung Fixed Burn Rate keuanganmu.
        </p>
        <button type="button" wire:click="openCreateModal"
            class="px-4 py-2 rounded-xl bg-[#C6F24D] text-slate-950 font-black text-xs hover:bg-[#B5E63B] shadow-2xs transition-all mt-2">
            + Tambah Langganan Pertama
        </button>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($subscriptions as $sub)
        @php
            $days = $sub->days_remaining;
            $isPaidThisMonth = $sub->is_paid_this_month;
            $isDueSoon = !$isPaidThisMonth && $sub->status === 'active' && $days >= 0 && $days <= 7;
        @endphp
        <div class="p-5 rounded-3xl bg-white border {{ $isPaidThisMonth ? 'border-emerald-200 ring-1 ring-emerald-100' : ($isDueSoon ? 'border-amber-300 ring-2 ring-amber-100' : 'border-slate-200/90') }} shadow-2xs space-y-4 hover:border-slate-300 transition-all flex flex-col justify-between"
             x-data="{ showHistory: false }">
            
            <div class="space-y-3">
                <!-- Top Row: Icon, Name, Status Badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-white shadow-2xs shrink-0"
                             style="background-color: {{ $sub->color ?: '#0F172A' }}">
                            <x-icon :name="$sub->icon ?: 'repeat'" class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-extrabold text-slate-950 truncate">{{ $sub->name }}</h4>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-medium">
                                <span class="capitalize">{{ $sub->billing_cycle }}</span>
                                <span>•</span>
                                <span>Tgl {{ $sub->billing_date }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Toggle Badge -->
                    <button type="button" wire:click="toggleStatus({{ $sub->id }})"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all cursor-pointer {{ $sub->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}"
                        title="Klik untuk mengubah status">
                        {{ $sub->status === 'active' ? 'Aktif' : 'Dijeda' }}
                    </button>
                </div>

                <!-- Price & Account -->
                <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between">
                    <div>
                        <div class="text-xl font-black font-mono text-slate-950">
                            Rp {{ number_format($sub->amount, 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-slate-400">
                            {{ $sub->billing_cycle === 'yearly' ? '≈ Rp ' . number_format($sub->monthly_equivalent, 0, ',', '.') . '/bln' : 'per ' . $sub->billing_cycle }}
                        </span>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] font-mono font-bold text-slate-500 block truncate max-w-[120px]">
                            {{ $sub->account?->name ?? 'Belum ada akun' }}
                        </span>
                        <span class="text-[9px] text-slate-400">
                            {{ $sub->category?->name ?? 'Kategori Umum' }}
                        </span>
                    </div>
                </div>

                <!-- Due Date Countdown Pill / Paid Status -->
                <div class="p-2.5 rounded-xl {{ $isPaidThisMonth ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : ($isDueSoon ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-slate-50 text-slate-600') }} flex items-center justify-between text-xs">
                    <span class="font-medium text-[11px]">
                        {{ $isPaidThisMonth ? 'Status Bulan Ini:' : 'Jatuh Tempo:' }}
                    </span>
                    <span class="font-bold text-[11px] font-mono">
                        @if($isPaidThisMonth)
                            <span class="text-emerald-700 font-extrabold flex items-center gap-1">
                                <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600" />
                                <span>Lunas ({{ $sub->last_billed_at->translatedFormat('d M') }})</span>
                            </span>
                        @elseif($sub->status === 'paused')
                            <span class="text-slate-400">Langganan Dijeda</span>
                        @elseif($days === 0)
                            <strong class="text-rose-600">Hari ini! ({{ $sub->next_billing_date->translatedFormat('d M') }})</strong>
                        @elseif($days > 0)
                            {{ $days }} hari lagi ({{ $sub->next_billing_date->translatedFormat('d M') }})
                        @else
                            {{ $sub->next_billing_date->translatedFormat('d M Y') }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="space-y-3">
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    
                    <!-- Left Action: Record Pay OR Paid Button -->
                    @if($isPaidThisMonth)
                    <button type="button" wire:click="recordPayment({{ $sub->id }})"
                        class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-900 font-black text-xs flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95 transition-all"
                        title="Klik jika ingin mencatat pembayaran ulang">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Lunas Bulan Ini</span>
                    </button>
                    @else
                    <button type="button" wire:click="recordPayment({{ $sub->id }})"
                        class="px-3 py-1.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] font-extrabold text-xs flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95 transition-all">
                        <x-icon name="check" class="w-3.5 h-3.5 text-[#C6F24D]" strokeWidth="2.5" />
                        <span>Catat Bayar</span>
                    </button>
                    @endif

                    <!-- Right Controls: History Dropdown, Edit, Delete -->
                    <div class="flex items-center gap-1">
                        <!-- History Dropdown Toggle -->
                        <button type="button" @click="showHistory = !showHistory"
                            class="px-2 py-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg text-[11px] font-bold flex items-center gap-1 transition-colors cursor-pointer"
                            title="Lihat Riwayat Pembayaran">
                            <x-icon name="clock" class="w-3.5 h-3.5 text-slate-400" />
                            <span>Riwayat</span>
                            <x-icon name="chevron-down" class="w-3 h-3 transition-transform duration-200" ::class="showHistory ? 'rotate-180' : ''" />
                        </button>

                        <button type="button" wire:click="openEditModal({{ $sub->id }})"
                            class="p-1.5 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer"
                            title="Edit Langganan">
                            <x-icon name="edit" class="w-4 h-4" />
                        </button>
                        <button type="button" wire:click="deleteSubscription({{ $sub->id }})"
                            wire:confirm="Hapus langganan '{{ $sub->name }}'?"
                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                            title="Hapus Langganan">
                            <x-icon name="trash-2" class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Collapsible Payment History Drawer -->
                <div x-show="showHistory" x-collapse x-cloak class="pt-2.5 border-t border-slate-100 space-y-2 animate-fade-in">
                    <div class="flex items-center justify-between text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">
                        <span>Riwayat Pembayaran</span>
                        <span>{{ $sub->payment_history->count() }} Record</span>
                    </div>

                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                        @forelse($sub->payment_history as $hist)
                        <div class="p-2 rounded-xl bg-slate-50/80 border border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></div>
                                <div>
                                    <span class="font-bold text-slate-900 text-[11px] block">
                                        {{ \Carbon\Carbon::parse($hist->date)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block truncate max-w-[130px]">
                                        {{ $hist->account?->name ?? 'Kas Utama' }}
                                    </span>
                                </div>
                            </div>
                            <span class="font-mono font-black text-slate-900 text-xs">
                                Rp {{ number_format($hist->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        @empty
                        <div class="py-3 text-center text-[11px] text-slate-400 italic bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                            Belum ada riwayat pembayaran tercatat.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
        @endforeach
    </div>
    @endif

    <!-- MODAL TAMBAH / EDIT LANGGANAN -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in"
         x-data="{
             formatNominal(val) {
                 if (!val) return '';
                 let clean = String(val).replace(/\D/g, '');
                 if (!clean) return '';
                 return new Intl.NumberFormat('id-ID').format(clean);
             }
         }"
         x-cloak>
        <div class="relative w-full max-w-lg bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col anim-scale-up">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black shadow-2xs">
                        <x-icon name="repeat" class="w-5 h-5" strokeWidth="2.2" />
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-950">
                            {{ $subscriptionId ? 'Edit Langganan' : 'Tambah Langganan Rutin' }}
                        </h3>
                        <p class="text-xs text-slate-500">Lacak pengeluaran berkala & jadwal tagihan</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4 overflow-y-auto">
                
                <!-- Quick Preset Chips -->
                @if(!$subscriptionId)
                <div>
                    <label class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Pilihan Cepat (Presets)</label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($presets as $idx => $pr)
                        <button type="button" wire:click="applyPreset({{ $idx }})"
                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1 active:scale-95">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $pr['color'] }}"></span>
                            <span>{{ $pr['name'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Layanan / Tagihan *</label>
                        <input type="text" wire:model.live.debounce.300ms="name" placeholder="e.g. ChatGPT Plus / Netflix / Indihome"
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                        @error('name') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Biaya (Rp) *</label>
                            <input type="text" 
                                   inputmode="numeric"
                                   wire:model="amount" 
                                   x-on:input="$el.value = formatNominal($el.value)"
                                   placeholder="350.000"
                                   class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                            @error('amount') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Siklus Pembayaran *</label>
                            <select wire:model="billing_cycle"
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white cursor-pointer">
                                <option value="monthly">Bulanan (Monthly)</option>
                                <option value="yearly">Tahunan (Yearly)</option>
                                <option value="weekly">Mingguan (Weekly)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Tagihan (1-31) *</label>
                            <input type="number" min="1" max="31" wire:model="billing_date" placeholder="15"
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                            @error('billing_date') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                            <select wire:model="status"
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white cursor-pointer">
                                <option value="active">Aktif</option>
                                <option value="paused">Dijeda (Paused)</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Rekening Pembayaran</label>
                            <select wire:model="account_id"
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white cursor-pointer">
                                <option value="">Pilih Rekening (Opsional)</option>
                                @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700">Pos Kategori Anggaran</label>
                                <span class="text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Auto-Detect</span>
                            </div>
                            <select wire:model="category_id"
                                class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white cursor-pointer">
                                <option value="">Pilih Kategori (Opsional)</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="notes" placeholder="e.g. Pembayaran otomatis via kartu kredit / debit"
                            class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-white flex items-center justify-end gap-2 shrink-0">
                <button type="button" wire:click="$set('isModalOpen', false)"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="saveSubscription"
                    class="px-5 py-2.5 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-black hover:bg-slate-800 cursor-pointer shadow-sm active-tap">
                    {{ $subscriptionId ? 'Simpan Perubahan' : 'Tambahkan Langganan' }}
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
