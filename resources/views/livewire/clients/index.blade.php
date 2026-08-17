<div class="space-y-4 sm:space-y-6">
    
    <!-- TOP PIUTANG SUMMARY BAR (Clean Responsive Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs">
            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Piutang Belum Lunas</span>
            <div class="text-lg sm:text-xl font-extrabold font-mono text-amber-600 mt-0.5">
                Rp {{ number_format($totalReceivables, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Invoice terkirim ke klien</span>
        </div>

        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs">
            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Piutang Jatuh Tempo (Overdue)</span>
            <div class="text-lg sm:text-xl font-extrabold font-mono text-rose-600 mt-0.5">
                Rp {{ number_format($overdueTotal, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 font-medium">Perlu follow-up penagihan</span>
        </div>

        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Klien Terdaftar</span>
                <div class="text-lg sm:text-xl font-extrabold font-mono text-slate-900 mt-0.5">
                    {{ $clients->count() }} Klien
                </div>
            </div>
            <button wire:click="openCreateClientModal" 
                    type="button"
                    class="px-3.5 py-2 rounded-xl sm:rounded-2xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-bold active-tap flex items-center gap-1 cursor-pointer shadow-2xs">
                <x-icon name="plus" class="w-3.5 h-3.5 text-[#C6F24D]" strokeWidth="2.5" />
                <span>Klien</span>
            </button>
        </div>
    </div>

    <!-- INVOICE PAYMENT AGING BREAKDOWN (0-15d, 16-30d, >30d) -->
    @if($totalReceivables > 0)
    <div class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Invoice Aging & Follow-Up Radar</h4>
            </div>
            <span class="text-[10px] font-mono text-slate-400">Total Piutang: Rp {{ number_format($totalReceivables, 0, ',', '.') }}</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <!-- 1. Current -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Belum Tempo</span>
                </div>
                <div class="text-xs sm:text-sm font-black font-mono text-slate-900">
                    Rp {{ number_format($agingCurrent, 0, ',', '.') }}
                </div>
            </div>

            <!-- 2. 1-15 Days -->
            <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/70 space-y-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Lewat 1-15 Hari</span>
                </div>
                <div class="text-xs sm:text-sm font-black font-mono text-amber-900">
                    Rp {{ number_format($aging1to15, 0, ',', '.') }}
                </div>
            </div>

            <!-- 3. 16-30 Days -->
            <div class="p-3 rounded-xl bg-orange-50/70 border border-orange-200/70 space-y-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-orange-800">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span>Lewat 16-30 Hari</span>
                </div>
                <div class="text-xs sm:text-sm font-black font-mono text-orange-900">
                    Rp {{ number_format($aging16to30, 0, ',', '.') }}
                </div>
            </div>

            <!-- 4. >30 Days -->
            <div class="p-3 rounded-xl bg-rose-50/70 border border-rose-200/70 space-y-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-rose-800">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <span>Kritis (>30 Hari)</span>
                </div>
                <div class="text-xs sm:text-sm font-black font-mono text-rose-900">
                    Rp {{ number_format($agingOver30, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- INVOICE LIST -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 sm:space-y-4">
        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Daftar Invoice & Status Penagihan</h3>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs font-medium">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">No. Invoice</th>
                        <th class="py-3.5 px-6">Project & Klien</th>
                        <th class="py-3.5 px-6">Jatuh Tempo</th>
                        <th class="py-3.5 px-6 text-right">Nominal</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Aksi & Follow-Up</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                    @php
                        $waLink = $this->getWhatsAppLink($inv);
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-6 font-mono font-bold text-slate-900">
                            {{ $inv->invoice_number }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-slate-900 block">{{ $inv->project->name ?? '-' }}</span>
                            <span class="text-[11px] text-slate-400">{{ $inv->project->client->name ?? '-' }} {{ $inv->project->client->phone ? '• ' . $inv->project->client->phone : '' }}</span>
                        </td>
                        <td class="py-4 px-6 text-slate-600 font-mono">
                            {{ \Carbon\Carbon::parse($inv->due_date)->translatedFormat('d M Y') }}
                            @if($inv->is_overdue)
                                <span class="text-rose-600 font-bold ml-1">(! Overdue)</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-extrabold text-slate-900">
                            Rp {{ number_format($inv->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($inv->status === 'paid')
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-800">Lunas</span>
                            @elseif($inv->is_overdue)
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-800">Overdue</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">Terkirim</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($inv->status !== 'paid')
                                    @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" 
                                       class="px-2.5 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-[11px] flex items-center gap-1 shadow-2xs active:scale-95 transition-all"
                                       title="Follow up invoice via WhatsApp">
                                        <x-icon name="message-circle" class="w-3.5 h-3.5 text-emerald-600" />
                                        <span>WA</span>
                                    </a>
                                    @endif

                                    <button wire:click="openMarkPaidModal({{ $inv->id }})" class="px-3 py-1 rounded-xl bg-[#C6F24D] hover:bg-[#B5E63B] text-slate-950 font-bold text-[11px] shadow-sm active-tap cursor-pointer">
                                        ✓ Lunas
                                    </button>
                                @else
                                    <span class="text-[11px] text-slate-400 font-mono">Lunas {{ $inv->paid_at ? \Carbon\Carbon::parse($inv->paid_at)->translatedFormat('d M') : '' }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-slate-400">Belum ada invoice dibuat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden space-y-2.5">
            @forelse($invoices as $inv)
            @php
                $waLink = $this->getWhatsAppLink($inv);
            @endphp
            <div class="p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-xs text-slate-900">{{ $inv->invoice_number }}</span>
                    @if($inv->status === 'paid')
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-800">Lunas</span>
                    @elseif($inv->is_overdue)
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-800">Overdue</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">Terkirim</span>
                    @endif
                </div>
                <div class="text-xs">
                    <span class="font-bold text-slate-900 block">{{ $inv->project->name ?? '-' }}</span>
                    <span class="text-[11px] text-slate-400">Klien: {{ $inv->project->client->name ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 font-mono text-xs">
                    <span class="text-slate-400 text-[10px]">Due: {{ \Carbon\Carbon::parse($inv->due_date)->translatedFormat('d M') }}</span>
                    <span class="font-black text-slate-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</span>
                </div>
                
                @if($inv->status !== 'paid')
                <div class="flex items-center justify-end gap-2 pt-1">
                    @if($waLink)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" 
                       class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-1.5 shadow-2xs">
                        <x-icon name="message-circle" class="w-3.5 h-3.5 text-emerald-600" />
                        <span>Kirim WA</span>
                    </a>
                    @endif
                    <button wire:click="openMarkPaidModal({{ $inv->id }})" class="px-3.5 py-1.5 rounded-xl bg-[#C6F24D] text-slate-950 font-bold text-xs shadow-2xs">
                        ✓ Tandai Lunas
                    </button>
                </div>
                @endif
            </div>
            @empty
            <div class="p-6 text-center text-slate-400 text-xs">Belum ada invoice dibuat.</div>
            @endforelse
        </div>
    </div>

    <!-- CLIENTS DIRECTORY -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 sm:space-y-4">
        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Direktori Klien</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
            @foreach($clients as $c)
            <div class="p-3.5 sm:p-4 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 space-y-1.5">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm truncate">{{ $c->name }}</h4>
                    <span class="text-[8px] sm:text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 shrink-0">{{ $c->company ?? 'Individu' }}</span>
                </div>
                <div class="text-[11px] text-slate-400 space-y-0.5">
                    @if($c->phone)<p class="truncate">📞 {{ $c->phone }}</p>@endif
                    @if($c->email)<p class="truncate">✉️ {{ $c->email }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL 1: ADD CLIENT -->
    <div x-data="{ open: @entangle('isClientModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isClientModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Tambah Data Klien</h3>
                <button wire:click="$set('isClientModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors"><x-icon name="x" class="w-4 h-4" /></button>
            </div>
            <form wire:submit.prevent="saveClient" class="p-5 space-y-3.5 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kontak Klien *</label>
                    <input type="text" wire:model.defer="name" placeholder="e.g. Pak Budi / PT Media Kreasi" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Perusahaan / Brand</label>
                    <input type="text" wire:model.defer="company" placeholder="e.g. Media Kreasi Group" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp</label>
                        <input type="text" wire:model.defer="phone" placeholder="0812..." class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                        <input type="email" wire:model.defer="email" placeholder="klien@..." class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isClientModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-slate-950 text-white text-xs font-extrabold shadow-sm active-tap">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: CONFIRM MARK PAID -->
    <div x-data="{ open: @entangle('isPaidModalOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak>
        <div @click.outside="$wire.set('isPaidModalOpen', false)" class="relative w-full max-w-md bg-white border-t sm:border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <!-- Drag indicator (mobile only) -->
            <div class="sm:hidden w-10 h-1 bg-slate-200 rounded-full mx-auto my-2"></div>

            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Konfirmasi Pelunasan Invoice</h3>
                <button wire:click="$set('isPaidModalOpen', false)" type="button" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition-colors"><x-icon name="x" class="w-4 h-4" /></button>
            </div>
            <form wire:submit.prevent="confirmMarkPaid" class="p-5 space-y-3.5 overflow-y-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Masuk ke Rekening *</label>
                    <select wire:model.defer="payAccountId" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pelunasan *</label>
                    <input type="date" wire:model.defer="paid_date" class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl sm:rounded-2xl px-3 py-2 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-slate-950">
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2 shrink-0">
                    <button type="button" wire:click="$set('isPaidModalOpen', false)" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl sm:rounded-2xl bg-[#C6F24D] text-slate-950 text-xs font-extrabold shadow-sm active-tap">Proses Pelunasan</button>
                </div>
            </form>
        </div>
    </div>

</div>
