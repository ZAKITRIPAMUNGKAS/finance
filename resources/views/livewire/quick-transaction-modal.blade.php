<div x-data="quickTransactionModal()" x-init="init()">

    <!-- Backdrop & Modal Container -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4"
         x-cloak>

        <div @click.outside="$wire.closeModal()"
             x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-y-0 sm:scale-100"
             x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md mx-auto bg-white border-t sm:border border-slate-200/80 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

            <!-- Header -->
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl {{ $type === 'income' ? 'bg-[#C6F24D] text-slate-950' : ($type === 'expense' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700') }} flex items-center justify-center font-bold shadow-xs">
                        @if($type === 'income')
                            <x-icon name="arrow-down-left" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
                        @elseif($type === 'expense')
                            <x-icon name="arrow-up-right" class="w-4 h-4 text-rose-700" strokeWidth="2.5" />
                        @else
                            <x-icon name="arrow-right-left" class="w-4 h-4 text-blue-700" strokeWidth="2.5" />
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-950 tracking-tight">
                            @if($type === 'income') Catat Pemasukan / Honor Proyek
                            @elseif($type === 'expense') Catat Pengeluaran Harian
                            @else Catat Mutasi Transfer
                            @endif
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium">Bicara Suara (Voice), Scan Foto / Bukti, atau Input Manual</p>
                    </div>
                </div>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- Body Content -->
            <div class="px-5 py-4 space-y-4 overflow-y-auto">

                <!-- 1. Transaction Type Segmented Toggle -->
                <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-2xl">
                    <button type="button" 
                            wire:click="setType('expense')" 
                            class="py-2 text-xs font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $type === 'expense' ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-500 hover:text-slate-950' }}">
                        <x-icon name="arrow-up-right" class="w-3.5 h-3.5" />
                        <span>Pengeluaran</span>
                    </button>
                    <button type="button" 
                            wire:click="setType('income')" 
                            class="py-2 text-xs font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $type === 'income' ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-950' }}">
                        <x-icon name="arrow-down-left" class="w-3.5 h-3.5" strokeWidth="2.5" />
                        <span>Pemasukan</span>
                    </button>
                    <button type="button" 
                            wire:click="setType('transfer')" 
                            class="py-2 text-xs font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $type === 'transfer' ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-500 hover:text-slate-950' }}">
                        <x-icon name="arrow-right-left" class="w-3.5 h-3.5" />
                        <span>Transfer</span>
                    </button>
                </div>

                <!-- 2. SMART QUICK-CAPTURE: VOICE & SCAN BAR -->
                <div class="bg-[#F8F9FA] border border-slate-200/80 rounded-2xl p-3.5 space-y-3">
                    
                    <!-- VOICE INPUT BAR (Prominent & Real-time) -->
                    <div class="p-3 bg-white border border-slate-200/90 rounded-xl space-y-2.5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" :class="recordingVoice ? 'bg-rose-500 animate-ping' : 'bg-emerald-500'"></div>
                                <span class="text-xs font-extrabold text-slate-900">
                                    @if($type === 'income') Input Suara Pemasukan (AI Voice)
                                    @elseif($type === 'expense') Input Suara Pengeluaran (AI Voice)
                                    @else Input Suara Transfer (AI Voice)
                                    @endif
                                </span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bahasa Indonesia</span>
                        </div>

                        <!-- Voice Trigger & Live Sound Wave -->
                        <div>
                            <button type="button" 
                                    @click="toggleVoice()" 
                                    :class="recordingVoice ? 'bg-rose-500 hover:bg-rose-600 text-white animate-pulse' : '{{ $type === 'income' ? 'bg-slate-950 hover:bg-slate-800 text-[#C6F24D]' : 'bg-slate-950 hover:bg-slate-800 text-white' }}'"
                                    class="w-full py-2.5 px-4 rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 transition-all shadow-sm cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" :class="recordingVoice ? 'animate-bounce' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                    <line x1="12" y1="19" x2="12" y2="22"/>
                                </svg>
                                <span x-text="recordingVoice ? 'Mendengarkan... (Klik Selesai)' : '🎙️ Bicara untuk Mencatat {{ $type === 'income' ? 'Pemasukan' : ($type === 'expense' ? 'Pengeluaran' : 'Transfer') }}'"></span>
                            </button>
                        </div>

                        <!-- Quick Voice Template Chips -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                            <span class="text-[10px] font-mono text-slate-400">Coba:</span>
                            @if($type === 'income')
                                <button type="button" wire:click="loadSampleVoice('project')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors cursor-pointer">
                                    + DP Project 5 Jt (BCA)
                                </button>
                                <button type="button" wire:click="loadSampleVoice('pelunasan')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors cursor-pointer">
                                    + Pelunasan 2.5 Jt (Mandiri)
                                </button>
                                <button type="button" wire:click="loadSampleVoice('gaji')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors cursor-pointer">
                                    + Gaji 7.5 Jt
                                </button>
                            @elseif($type === 'expense')
                                <button type="button" wire:click="loadSampleVoice('kopi')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors cursor-pointer">
                                    + Kopi 42 Rb (GoPay)
                                </button>
                                <button type="button" wire:click="loadSampleVoice('bensin')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors cursor-pointer">
                                    + Bensin 50 Rb (Cash)
                                </button>
                                <button type="button" wire:click="loadSampleVoice('wifi')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors cursor-pointer">
                                    + Wifi 350 Rb
                                </button>
                            @endif
                        </div>

                        <!-- Live Voice Transcript / Status Box -->
                        <div x-show="recordingVoice || voiceStatus" x-cloak class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <div class="flex items-center gap-2 text-slate-700">
                                <template x-if="recordingVoice">
                                    <span class="flex gap-0.5 items-center">
                                        <span class="w-1 h-3 bg-rose-500 animate-pulse rounded-full"></span>
                                        <span class="w-1 h-4 bg-rose-500 animate-pulse delay-75 rounded-full"></span>
                                        <span class="w-1 h-2 bg-rose-500 animate-pulse delay-150 rounded-full"></span>
                                    </span>
                                </template>
                                <span class="font-mono text-slate-800 leading-relaxed" x-text="voiceStatus"></span>
                            </div>
                        </div>
                    </div>

                    <!-- SCAN FOTO STRUK / BUKTI TRANSFER TOGGLE -->
                    <div class="pt-1">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="camera" class="w-3.5 h-3.5 text-slate-700" />
                                <span class="text-xs font-bold text-slate-900">
                                    @if($type === 'income') Scan Bukti Transfer Klien / Mutasi Masuk
                                    @elseif($type === 'expense') Scan Foto Struk Belanja / Nota Kasir
                                    @else Scan Bukti Transfer Antar Rekening
                                    @endif
                                </span>
                            </div>
                            <button type="button" 
                                    wire:click="toggleManualTextMode" 
                                    class="text-[11px] font-bold text-teal-700 hover:underline cursor-pointer">
                                {{ $manualTextMode ? 'Tutup Paste' : 'Tempel Teks' }}
                            </button>
                        </div>

                        @if(!$manualTextMode)
                        <div class="space-y-2">
                            <!-- Upload Button -->
                            <label class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-slate-400 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-xs group">
                                <input type="file" 
                                       accept="image/*" 
                                       @change="scanFile($event)" 
                                       wire:model="receiptImage" 
                                       class="hidden">
                                <x-icon name="upload" class="w-3.5 h-3.5 text-slate-600 group-hover:scale-110 transition-transform" />
                                <span class="text-xs font-bold text-slate-700">
                                    @if($type === 'income') Pilih Foto Bukti Transfer M-Banking / Invoice
                                    @else Pilih Foto Struk / Gambar Nota Kasir
                                    @endif
                                </span>
                            </label>

                            <!-- Quick OCR Samples -->
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-mono text-slate-400">Contoh:</span>
                                @if($type === 'income')
                                    <button type="button" wire:click="loadSampleReceipt('transfer_klien')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors cursor-pointer">
                                        📄 Bukti Transfer BCA 7.5 Jt
                                    </button>
                                    <button type="button" wire:click="loadSampleReceipt('transfer_mandiri')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors cursor-pointer">
                                        📄 Bukti Transfer Mandiri 2.5 Jt
                                    </button>
                                @elseif($type === 'expense')
                                    <button type="button" wire:click="loadSampleReceipt('kopi')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors cursor-pointer">
                                        📄 Struk Kopi Kenangan
                                    </button>
                                    <button type="button" wire:click="loadSampleReceipt('indomaret')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors cursor-pointer">
                                        📄 Struk Indomaret
                                    </button>
                                    <button type="button" wire:click="loadSampleReceipt('adobe')" class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-colors cursor-pointer">
                                        📄 Invoice Adobe
                                    </button>
                                @endif
                            </div>
                        </div>
                        @else
                        <!-- Paste Text Field -->
                        <div class="space-y-2 pt-1">
                            <textarea wire:model="pastedText" 
                                      rows="2" 
                                      placeholder="{{ $type === 'income' ? 'Tempel teks notifikasi SMS Banking / WA transfer masuk, e.g. TRSF DARI BUDI RP 2.500.000...' : 'Tempel teks struk, SMS banking, atau bukti bayar di sini...' }}" 
                                      class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-slate-900"></textarea>
                            <button type="button" 
                                    wire:click="extractFromPastedText" 
                                    class="w-full py-2 bg-slate-950 text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition-colors cursor-pointer">
                                Ekstrak Teks Transaksi
                            </button>
                        </div>
                        @endif

                        <!-- OCR Scanner Animation from Library -->
                        <div x-show="scanning" x-cloak class="mt-2 p-2.5 bg-white border border-slate-200 rounded-xl flex items-center gap-2.5 text-xs font-mono text-slate-700">
                            <x-icon name="loader-2" class="w-4 h-4 animate-spin text-slate-950" />
                            <span x-text="ocrStatus">Membaca gambar struk / bukti transfer...</span>
                        </div>
                    </div>

                    <!-- Scan / Voice Success Notification Badge -->
                    @if($scanSuccessMessage)
                    <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-900 font-semibold">
                        <div class="flex items-center gap-2">
                            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>{{ $scanSuccessMessage }}</span>
                        </div>
                        <button type="button" wire:click="$set('scanSuccessMessage', null)" class="text-emerald-700 hover:text-emerald-950 font-bold ml-2 cursor-pointer">&times;</button>
                    </div>
                    @endif

                    <!-- Collapsible Raw Extracted Text -->
                    @if(!empty($rawScannedText))
                    <div class="pt-1">
                        <button type="button" 
                                @click="showRawText = !showRawText" 
                                class="text-[10px] font-mono text-slate-500 hover:text-slate-800 flex items-center gap-1 cursor-pointer">
                            <span x-text="showRawText ? '▼ Sembunyikan Teks OCR' : '▶ Lihat Teks Bersih Hasil Scan'"></span>
                        </button>
                        <div x-show="showRawText" x-cloak class="mt-1.5 p-2 bg-white border border-slate-200 rounded-xl text-[10px] font-mono text-slate-600 whitespace-pre-wrap max-h-28 overflow-y-auto">
                            {{ $rawScannedText }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- 3. Primary Big Nominal Input -->
                <div class="text-center pt-2">

                    {{-- Wishlist saving-mode banner --}}
                    @if($savingWishlistId)
                    <div class="mb-3 inline-flex items-center gap-2 px-3 py-1.5 bg-[#EBFAD2] border border-[#C6F24D] rounded-xl text-xs font-bold text-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.8 0 3 2 4.5V20h4v-2h3v2h4v-4c1-.5 1.7-1 2-2h2v-4h-2c0-1-.5-1.5-1-2V5z"/></svg>
                        Mode Saving Wishlist — catat setor dana
                    </div>
                    @endif

                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Nominal Transaksi</span>
                    <div class="flex items-center justify-center">
                        <span class="text-2xl font-black text-slate-400 mr-2">Rp</span>
                        <input type="text" 
                               inputmode="numeric"
                               wire:model.live.debounce.400ms="amount" 
                               placeholder="0" 
                               class="w-full max-w-[240px] text-center text-4xl sm:text-5xl font-black font-mono text-slate-950 placeholder-slate-300 focus:outline-none bg-transparent border-b-2 border-slate-200 focus:border-slate-950 pb-1 tracking-tight">
                    </div>
                    @error('amount') <span class="text-xs text-rose-500 mt-1.5 block font-semibold">{{ $message }}</span> @enderror

                    <!-- Quick Amount Chips -->
                    <div class="flex items-center justify-center gap-1.5 flex-wrap mt-3">
                        @foreach([50000 => '50rb', 100000 => '100rb', 500000 => '500rb', 1000000 => '1jt', 2500000 => '2.5jt'] as $val => $lbl)
                        <button type="button" 
                                wire:click="$set('amount', '{{ $val }}')" 
                                class="px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold font-mono text-slate-700 transition-colors cursor-pointer">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Budget Impact Bar --}}
                    @if($budgetImpact)
                    <div class="mt-3 mx-auto max-w-xs text-left p-3 rounded-2xl border text-xs
                        {{ $budgetImpact['status'] === 'over'
                            ? 'bg-rose-50 border-rose-200'
                            : ($budgetImpact['status'] === 'warning'
                                ? 'bg-amber-50 border-amber-200'
                                : 'bg-emerald-50 border-emerald-200') }}">
                        <div class="flex items-center justify-between mb-1.5 font-bold
                            {{ $budgetImpact['status'] === 'over'
                                ? 'text-rose-800'
                                : ($budgetImpact['status'] === 'warning'
                                    ? 'text-amber-800'
                                    : 'text-emerald-800') }}">
                            <span>
                                @if($budgetImpact['status'] === 'over')
                                    ⚠️ Melebihi Budget!
                                @elseif($budgetImpact['status'] === 'warning')
                                    ⚡ Hampir Habis
                                @else
                                    ✅ Dalam Budget
                                @endif
                            </span>
                            <span class="font-mono">{{ $budgetImpact['percent_used'] }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-white/60 rounded-full overflow-hidden mb-1.5 border border-slate-200/60">
                            <div class="h-full rounded-full transition-all duration-500
                                {{ $budgetImpact['status'] === 'over'
                                    ? 'bg-rose-500'
                                    : ($budgetImpact['status'] === 'warning'
                                        ? 'bg-amber-400'
                                        : 'bg-emerald-500') }}"
                                style="width: {{ min(100, $budgetImpact['percent_used']) }}%">
                            </div>
                        </div>
                        <div class="flex justify-between text-[10px] font-mono font-semibold
                            {{ $budgetImpact['status'] === 'over' ? 'text-rose-700' : 'text-slate-600' }}">
                            <span>Terpakai: Rp {{ number_format($budgetImpact['after_spend'], 0, ',', '.') }}</span>
                            <span>Budget: Rp {{ number_format($budgetImpact['allocated'], 0, ',', '.') }}</span>
                        </div>
                        @if($budgetImpact['status'] !== 'over')
                        <div class="text-[10px] text-slate-500 mt-0.5 font-mono">
                            Sisa: <strong class="text-emerald-700">Rp {{ number_format($budgetImpact['remaining'], 0, ',', '.') }}</strong>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- 4. Clean Form Fields -->
                <form wire:submit.prevent="save" class="space-y-3.5 pt-2">
                    <!-- Description & Date -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Transaksi *</label>
                            <input type="text" 
                                   wire:model="description" 
                                   placeholder="e.g. Kopi Kenangan / DP Klien / Sewa Lensa" 
                                   class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                            @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" 
                                   wire:model="date" 
                                   class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                            @error('date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Account Selection -->
                    <div class="grid grid-cols-1 {{ $type === 'transfer' ? 'sm:grid-cols-2' : '' }} gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">{{ $type === 'transfer' ? 'Dari Rekening Sumber' : 'Rekening / Dompet' }}</label>
                            <select wire:model="account_id" 
                                    class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('account_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        @if($type === 'transfer')
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ke Rekening Tujuan</label>
                            <select wire:model="destination_account_id" 
                                    class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                                <option value="">Pilih Akun Tujuan...</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('destination_account_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>

                    @if($type !== 'transfer')
                    <!-- Category & Project Relation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                            <select wire:model="category_id" 
                                    class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                                <option value="">Pilih Kategori...</option>
                                @php
                                    $bizCategories = $categories->where('is_business', true);
                                    $personalCategories = $categories->where('is_business', false);
                                @endphp

                                @if($bizCategories->count() > 0)
                                    <optgroup label="── Kategori Bisnis / Freelance ──">
                                        @foreach($bizCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif

                                @if($personalCategories->count() > 0)
                                    <optgroup label="── Kategori Personal / Pribadi ──">
                                        @foreach($personalCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Project Terkait (Opsional)</label>
                            <select wire:model="project_id" 
                                    class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                                <option value="">Bukan Biaya Project</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->name }} ({{ $proj->client->name ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-6 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] text-slate-950 text-xs font-extrabold shadow-sm active-tap transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <x-icon name="check" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
                            <span>Simpan Transaksi</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function quickTransactionModal() {
            return {
                open: @entangle('isOpen'),
                scanning: false,
                ocrStatus: '',
                showRawText: false,
                previewUrl: @entangle('previewImage'),
                
                // Voice State
                recordingVoice: false,
                voiceStatus: '',
                voiceTranscript: '',
                recognition: null,
                speechSupported: false,

                init() {
                    this.initTesseract();
                    this.initSpeechRecognition();

                    window.addEventListener('start-voice-listening', () => {
                        setTimeout(() => {
                            this.startVoice();
                        }, 300);
                    });
                },

                initTesseract() {
                    if (!window.Tesseract && !document.getElementById('tesseract-script')) {
                        const script = document.createElement('script');
                        script.id = 'tesseract-script';
                        script.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
                        document.head.appendChild(script);
                    }
                },

                initSpeechRecognition() {
                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    if (SpeechRecognition) {
                        this.speechSupported = true;
                        this.recognition = new SpeechRecognition();
                        this.recognition.lang = 'id-ID';
                        this.recognition.interimResults = true;
                        this.recognition.maxAlternatives = 1;
                        this.recognition.continuous = false;

                        this.recognition.onstart = () => {
                            this.recordingVoice = true;
                            this.voiceStatus = '🎙️ Sedang mendengarkan... Silakan bicara.';
                            this.voiceTranscript = '';
                        };

                        this.recognition.onresult = (event) => {
                            let interim = '';
                            let final = '';
                            for (let i = 0; i < event.results.length; ++i) {
                                if (event.results[i].isFinal) {
                                    final += event.results[i][0].transcript;
                                } else {
                                    interim += event.results[i][0].transcript;
                                }
                            }
                            const captured = (final || interim).trim();
                            if (captured) {
                                this.voiceTranscript = captured;
                                this.voiceStatus = '🎙️ "' + captured + '"';
                            }
                        };

                        this.recognition.onerror = (event) => {
                            console.warn('Speech recognition error:', event.error);
                            this.recordingVoice = false;
                            if (event.error === 'not-allowed' || event.error === 'permission-denied') {
                                this.voiceStatus = '⚠️ Izin mikrofon belum diberikan. Silakan klik ikon gembok di address bar browser untuk mengizinkan mikrofon.';
                            } else if (event.error === 'no-speech') {
                                this.voiceStatus = '⚠️ Suara tidak terdeteksi. Silakan klik tombol dan coba bicara lagi.';
                            } else if (event.error === 'network') {
                                this.voiceStatus = '⚠️ Gangguan koneksi speech recognition. Pastikan internet aktif.';
                            } else if (event.error === 'audio-capture') {
                                this.voiceStatus = '⚠️ Mikrofon tidak terdeteksi atau sedang dipakai aplikasi lain.';
                            } else {
                                this.voiceStatus = '⚠️ Status: ' + event.error;
                            }
                        };

                        this.recognition.onend = () => {
                            this.recordingVoice = false;
                            if (this.voiceTranscript && this.voiceTranscript.trim().length >= 2) {
                                this.voiceStatus = '⚡ Memproses: "' + this.voiceTranscript + '"';
                                this.$wire.processVoiceInput(this.voiceTranscript.trim());
                            }
                        };
                    } else {
                        this.speechSupported = false;
                        this.voiceStatus = 'Browser ini belum mendukung Web Speech API (Gunakan Google Chrome, Microsoft Edge, atau Safari).';
                    }
                },

                toggleVoice() {
                    if (this.recordingVoice) {
                        this.stopVoice();
                    } else {
                        this.startVoice();
                    }
                },

                startVoice() {
                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    if (!SpeechRecognition) {
                        this.voiceStatus = '⚠️ Browser Anda tidak mendukung Web Speech API. Silakan gunakan Google Chrome atau Microsoft Edge.';
                        return;
                    }
                    
                    if (!this.recognition) {
                        this.initSpeechRecognition();
                    }

                    this.voiceTranscript = '';
                    this.voiceStatus = '🎙️ Menyiapkan mikrofon... Silakan bicara.';

                    try {
                        this.recognition.start();
                    } catch (err) {
                        console.warn('Recognition start caught:', err);
                        try {
                            this.recognition.abort();
                        } catch (e) {}
                        setTimeout(() => {
                            try {
                                this.recognition.start();
                            } catch (e) {
                                console.error('Recognition retry failed:', e);
                                this.voiceStatus = '⚠️ Gagal memulai mikrofon: ' + (e.message || 'Klik lagi untuk mencoba');
                            }
                        }, 120);
                    }
                },

                stopVoice() {
                    if (this.recognition) {
                        try {
                            this.recognition.stop();
                        } catch (e) {}
                    }
                    this.recordingVoice = false;
                },

                preprocessImage(imgData, callback) {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        let width = img.width;
                        let height = img.height;
                        const maxDim = 1600;
                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;

                        ctx.drawImage(img, 0, 0, width, height);
                        const imgDataObj = ctx.getImageData(0, 0, width, height);
                        const d = imgDataObj.data;
                        for (let i = 0; i < d.length; i += 4) {
                            const avg = (d[i] * 0.299 + d[i + 1] * 0.587 + d[i + 2] * 0.114);
                            const contrast = 1.3;
                            const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));
                            const highContrast = factor * (avg - 128) + 128;
                            const finalVal = Math.min(255, Math.max(0, highContrast));

                            d[i] = finalVal;
                            d[i + 1] = finalVal;
                            d[i + 2] = finalVal;
                        }
                        ctx.putImageData(imgDataObj, 0, 0);
                        callback(canvas.toDataURL('image/jpeg', 0.9));
                    };
                    img.src = imgData;
                },

                scanFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.scanning = true;
                    this.ocrStatus = 'Membaca gambar struk...';

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const rawImg = e.target.result;
                        this.previewUrl = rawImg;

                        this.preprocessImage(rawImg, (processedImg) => {
                            if (window.Tesseract) {
                                this.ocrStatus = 'Menganalisis teks struk...';
                                window.Tesseract.recognize(processedImg, 'eng', {
                                    logger: m => {
                                        if (m.status === 'recognizing text') {
                                            this.ocrStatus = 'Memindai teks (' + Math.round(m.progress * 100) + '%)...';
                                        }
                                    }
                                }).then(({ data: { text } }) => {
                                    this.ocrStatus = 'Menerapkan data...';
                                    if (text && text.trim().length > 2) {
                                        const cleanText = text.replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F\uD800-\uDFFF\uFFFD]/g, '');
                                        this.$wire.processScannedText(cleanText);
                                    }
                                    this.scanning = false;
                                }).catch(err => {
                                    console.error('OCR Error:', err);
                                    this.scanning = false;
                                });
                            } else {
                                setTimeout(() => {
                                    this.scanning = false;
                                }, 1000);
                            }
                        });
                    };
                    reader.readAsDataURL(file);
                }
            };
        }
    </script>
</div>
