<div class="space-y-4 sm:space-y-6">
    
    <!-- TOP STATS BAR (Compact 3-Col Responsive Grid for Mobile & Desktop) -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4">
        <!-- Income -->
        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-3 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Income In</span>
                <div class="text-xs sm:text-lg font-black font-mono text-emerald-600 truncate mt-0.5">
                    +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </div>
            </div>
            <div class="hidden sm:flex w-10 h-10 rounded-2xl bg-[#C6F24D] text-slate-950 items-center justify-center font-bold shadow-xs shrink-0">
                <x-icon name="arrow-down-left" class="w-4 h-4" strokeWidth="2.5" />
            </div>
        </div>

        <!-- Expense -->
        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-3 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Expense Out</span>
                <div class="text-xs sm:text-lg font-black font-mono text-slate-950 truncate mt-0.5">
                    -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
            </div>
            <div class="hidden sm:flex w-10 h-10 rounded-2xl bg-slate-100 text-slate-800 items-center justify-center font-bold shrink-0">
                <x-icon name="arrow-up-right" class="w-4 h-4" />
            </div>
        </div>

        <!-- Net Cashflow -->
        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-3 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Net Cashflow</span>
                <div class="text-xs sm:text-lg font-black font-mono {{ ($totalIncome - $totalExpense) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} truncate mt-0.5">
                    {{ ($totalIncome - $totalExpense) >= 0 ? '+' : '' }}Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                </div>
            </div>
            <div class="hidden sm:flex w-10 h-10 rounded-2xl bg-slate-950 text-[#C6F24D] items-center justify-center font-bold shadow-xs shrink-0">
                <x-icon name="activity" class="w-4 h-4" />
            </div>
        </div>
    </div>

    <!-- FILTER & ACTION BAR -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/70 shadow-xs space-y-3">
        <!-- Search & Actions Grid -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2.5 sm:gap-3">
            <!-- Search -->
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-2.5 text-slate-400">
                    <x-icon name="search" class="w-4 h-4" />
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari transaksi, toko, keterangan..." 
                       class="w-full bg-[#F8F9FA] border border-slate-200/80 rounded-xl sm:rounded-2xl pl-10 pr-4 py-2.5 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all">
            </div>

            <!-- Action CTAs (3 Side-by-Side buttons on mobile) -->
            <div class="grid grid-cols-3 gap-1.5 sm:flex sm:items-center sm:gap-2">
                <button wire:click="$dispatch('open-quick-voice')" 
                        type="button"
                        class="py-2.5 px-3 rounded-xl sm:rounded-2xl bg-white border border-slate-200/80 hover:border-slate-400 text-slate-800 text-[11px] sm:text-xs font-extrabold shadow-2xs active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                        title="Catat via Perintah Suara (Voice)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 sm:w-4 h-3.5 sm:h-4 text-rose-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                        <line x1="12" y1="19" x2="12" y2="22"/>
                    </svg>
                    <span>Suara</span>
                </button>

                <button wire:click="$dispatch('open-quick-add')" 
                        type="button"
                        class="py-2.5 px-3 rounded-xl sm:rounded-2xl bg-slate-950 text-white hover:bg-slate-800 text-[11px] sm:text-xs font-extrabold shadow-2xs active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <x-icon name="camera" class="w-3.5 sm:w-4 h-3.5 sm:h-4 text-[#C6F24D] shrink-0" strokeWidth="2.5" />
                    <span>Scan</span>
                </button>

                <button wire:click="$dispatch('open-quick-add')" 
                        type="button"
                        class="py-2.5 px-3 rounded-xl sm:rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] text-slate-950 text-[11px] sm:text-xs font-extrabold shadow-2xs active-tap transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <x-icon name="plus" class="w-3.5 sm:w-4 h-3.5 sm:h-4 text-slate-950 shrink-0" strokeWidth="2.5" />
                    <span>Manual</span>
                </button>
            </div>
        </div>

        <!-- Filter Pills with smooth scroll (No scrollbar track) -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar pt-1 pb-0.5">
            <button wire:click="$set('type', 'all')" 
                    type="button"
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $type === 'all' ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </button>
            <button wire:click="$set('type', 'income')" 
                    type="button"
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $type === 'income' ? 'bg-[#C6F24D] text-slate-950 shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pemasukan
            </button>
            <button wire:click="$set('type', 'expense')" 
                    type="button"
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $type === 'expense' ? 'bg-slate-950 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pengeluaran
            </button>
            <button wire:click="$set('type', 'transfer')" 
                    type="button"
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition-all shrink-0 cursor-pointer {{ $type === 'transfer' ? 'bg-slate-950 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Transfer
            </button>

            <!-- Month Dropdown -->
            <div class="ml-auto shrink-0">
                <input type="month" 
                       wire:model.live="month" 
                       class="bg-[#F8F9FA] border border-slate-200 rounded-xl px-2.5 py-1 text-xs font-bold text-slate-700 focus:outline-none focus:border-slate-900 cursor-pointer">
            </div>
        </div>
    </div>

    <!-- TRANSACTIONS CONTAINER -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl shadow-xs overflow-hidden">
        
        <!-- DESKTOP TABLE VIEW -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6">Deskripsi</th>
                        <th class="py-3.5 px-6">Kategori & Project</th>
                        <th class="py-3.5 px-6">Rekening</th>
                        <th class="py-3.5 px-6 text-right">Nominal</th>
                        <th class="py-3.5 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-6 font-mono text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 {{ $tx->type === 'income' ? 'bg-[#C6F24D] text-slate-950' : ($tx->type === 'expense' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800') }}">
                                    @if($tx->type === 'income') <x-icon name="arrow-down-left" class="w-4 h-4" strokeWidth="2.5" /> @elseif($tx->type === 'expense') <x-icon name="arrow-up-right" class="w-4 h-4" /> @else <x-icon name="arrow-right-left" class="w-4 h-4" /> @endif
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $tx->description }}</span>
                                    @if($tx->notes)
                                    <span class="text-[11px] text-slate-400">{{ $tx->notes }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $tx->category->name }}
                                </span>
                            @endif
                            @if($tx->project)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 ml-1">
                                    💼 {{ $tx->project->name }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-600">
                            @if($tx->type === 'transfer')
                                <span>{{ $tx->account->name ?? '-' }}</span> <span class="text-slate-400">&rarr;</span> <span>{{ $tx->destinationAccount->name ?? '-' }}</span>
                            @else
                                <span>{{ $tx->account->name ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-extrabold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-slate-950' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button wire:click="deleteTransaction({{ $tx->id }})" 
                                    wire:confirm="Hapus transaksi ini?" 
                                    class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                                    title="Hapus Transaksi">
                                <x-icon name="trash-2" class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-slate-400">Tidak ada transaksi ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE TRANSACTION LIST (Touch & Tap Ergonomic) -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($transactions as $tx)
            <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Icon Avatar -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 {{ $tx->type === 'income' ? 'bg-[#C6F24D] text-slate-950' : ($tx->type === 'expense' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-800') }}">
                        @if($tx->type === 'income') <x-icon name="arrow-down-left" class="w-4 h-4" strokeWidth="2.5" /> @elseif($tx->type === 'expense') <x-icon name="arrow-up-right" class="w-4 h-4" /> @else <x-icon name="arrow-right-left" class="w-4 h-4" /> @endif
                    </div>
                    
                    <!-- Info -->
                    <div class="min-w-0">
                        <span class="font-bold text-xs text-slate-900 block truncate">{{ $tx->description }}</span>
                        <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-400 mt-0.5">
                            <span class="font-mono">{{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M') }}</span>
                            <span>•</span>
                            <span class="truncate max-w-[90px]">{{ $tx->account->name ?? '-' }}</span>
                            @if($tx->category)
                            <span>•</span>
                            <span class="text-slate-600 font-semibold truncate max-w-[90px]">{{ $tx->category->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Nominal & Quick Action -->
                <div class="text-right shrink-0 flex flex-col items-end">
                    <span class="font-black text-xs font-mono block {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-slate-950' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </span>
                    <button wire:click="deleteTransaction({{ $tx->id }})" 
                            wire:confirm="Hapus transaksi ini?" 
                            type="button"
                            class="text-[10px] font-bold text-slate-400 hover:text-rose-600 py-0.5 px-1.5 rounded hover:bg-rose-50 transition-colors mt-0.5 cursor-pointer">
                        Hapus
                    </button>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-xs text-slate-400">Tidak ada transaksi ditemukan.</div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
