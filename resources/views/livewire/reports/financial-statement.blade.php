<div class="space-y-6 max-w-6xl mx-auto pb-12">
    
    <!-- HEADER INTRO (Clean Responsive FinTech Banner) -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-3.5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-[#C6F24D] text-slate-950 flex items-center justify-center font-bold shadow-2xs shrink-0">
                <x-icon name="file-text" class="w-5 h-5 sm:w-6 sm:h-6" strokeWidth="2.5" />
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">Laporan Arus Kas & Keuangan</h2>
                <p class="text-[11px] sm:text-xs text-slate-400">{{ $user->name }} • Ringkasan laba rugi, arus kas, dan daftar penagihan invoice {{ $startDate->translatedFormat('F Y') }}.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Period Filter -->
            <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-2 py-1">
                <select wire:model.live="month" class="bg-transparent text-xs font-bold text-slate-800 border-none focus:ring-0 cursor-pointer">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                    @endfor
                </select>
                <select wire:model.live="year" class="bg-transparent text-xs font-bold text-slate-800 border-none focus:ring-0 cursor-pointer">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Export CSV -->
            <a href="{{ route('reports.export-csv') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-all flex items-center gap-1.5 active:scale-95">
                <x-icon name="download" class="w-3.5 h-3.5" />
                <span>Unduh CSV</span>
            </a>

            <!-- Print Page -->
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold shadow-sm hover:bg-slate-800 transition-all flex items-center gap-1.5 active:scale-95">
                <x-icon name="printer" class="w-3.5 h-3.5" />
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- METRICS SUMMARY 4-CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Income -->
        <div class="bg-white border border-slate-200/70 rounded-2xl p-4 sm:p-5 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Pemasukan</span>
            <div class="text-lg sm:text-2xl font-black font-mono text-emerald-600">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">{{ $incomeTransactions->count() }} transaksi masuk</span>
        </div>

        <!-- Expense -->
        <div class="bg-white border border-slate-200/70 rounded-2xl p-4 sm:p-5 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Pengeluaran</span>
            <div class="text-lg sm:text-2xl font-black font-mono text-rose-600">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">{{ $expenseTransactions->count() }} transaksi keluar</span>
        </div>

        <!-- Net Profit -->
        <div class="bg-white border border-slate-200/70 rounded-2xl p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Laba Bersih (Net)</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ $profitMargin >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ $profitMargin }}%
                </span>
            </div>
            <div class="text-lg sm:text-2xl font-black font-mono {{ $netProfit >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">{{ $netProfit >= 0 ? 'Surplus Operasional' : 'Defisit Operasional' }}</span>
        </div>

        <!-- Liquid Cash -->
        <div class="bg-white border border-slate-200/70 rounded-2xl p-4 sm:p-5 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Saldo Kas & Bank</span>
            <div class="text-lg sm:text-2xl font-black font-mono text-slate-900">
                Rp {{ number_format($totalCash, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">{{ $accounts->count() }} akun dompet aktif</span>
        </div>
    </div>

    <!-- INVOICE LIST SECTION -->
    <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-slate-900 text-[#C6F24D] flex items-center justify-center font-bold text-xs">
                    <x-icon name="receipt" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Daftar Tagihan Invoice ({{ $startDate->translatedFormat('F Y') }})</h3>
                    <p class="text-[11px] text-slate-400">Semua invoice penagihan project yang diterbitkan pada periode ini.</p>
                </div>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-xl">
                {{ $invoices->count() }} Invoice
            </span>
        </div>

        @if($invoices->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-mono uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-3">No. Invoice</th>
                        <th class="py-3 px-3">Project & Klien</th>
                        <th class="py-3 px-3">Jatuh Tempo</th>
                        <th class="py-3 px-3 text-right">Nominal</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($invoices as $inv)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-3 font-mono font-bold text-slate-900">
                            {{ $inv->invoice_number }}
                        </td>
                        <td class="py-3.5 px-3">
                            <div class="font-bold text-slate-900">{{ $inv->project->name }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ $inv->project->client->name ?? 'Klien' }}
                                @if($inv->project->client?->company)
                                • {{ $inv->project->client->company }}
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-3 font-mono text-slate-600">
                            {{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}
                        </td>
                        <td class="py-3.5 px-3 text-right font-mono font-bold text-slate-900">
                            Rp {{ number_format($inv->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3 text-center">
                            @if($inv->status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-emerald-100 text-emerald-800">
                                    • LUNAS
                                </span>
                            @elseif($inv->is_overdue)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-rose-100 text-rose-800">
                                    • OVERDUE
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-amber-100 text-amber-800">
                                    • MENUNGGU
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <!-- Quick View In-Page Modal -->
                                <button wire:click="viewInvoice({{ $inv->id }})" 
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-[#C6F24D] text-xs font-bold transition-all shadow-2xs flex items-center gap-1 active:scale-95 cursor-pointer"
                                        title="Lihat Invoice di Halaman Ini">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                    <span>Lihat</span>
                                </button>

                                <!-- WhatsApp Share -->
                                <a href="{{ $inv->whatsapp_share_url }}" target="_blank"
                                   class="p-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors"
                                   title="Kirim ke WhatsApp Klien">
                                    <x-icon name="send" class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-8 text-center bg-slate-50 rounded-2xl border border-slate-100">
            <x-icon name="receipt" class="w-8 h-8 text-slate-300 mx-auto mb-2" />
            <p class="text-xs font-bold text-slate-600">Belum ada invoice yang diterbitkan pada bulan {{ $startDate->translatedFormat('F Y') }}.</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Invoice yang Anda buat di menu Project akan otomatis terdaftar di sini.</p>
        </div>
        @endif
    </div>

    <!-- 2-COL DETAILS: CASH FLOW & EXPENSES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 items-start">
        
        <!-- LEFT: EXPENSES BREAKDOWN -->
        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengeluaran per Kategori</span>
                <span class="text-xs font-mono font-bold text-slate-900">Total: Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>

            @if(count($expensesByCategory) > 0)
            <div class="space-y-3">
                @foreach($expensesByCategory as $catName => $catAmount)
                @php
                    $pct = $totalExpense > 0 ? round(($catAmount / $totalExpense) * 100, 1) : 0;
                @endphp
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-800">{{ $catName }}</span>
                        <span class="font-mono text-slate-900">Rp {{ number_format($catAmount, 0, ',', '.') }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-900 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-slate-400 py-4 text-center">Tidak ada catatan pengeluaran pada periode ini.</p>
            @endif
        </div>

        <!-- RIGHT: CASH & ACTIVE ACCOUNTS -->
        <div class="bg-white border border-slate-200/70 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Saldo Akun Dompet & Kas</span>
                <span class="text-xs font-mono font-bold text-emerald-700">Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
            </div>

            <div class="space-y-2.5">
                @foreach($accounts as $acc)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs">
                            {{ substr($acc->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $acc->name }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-mono">{{ $acc->type }} {{ $acc->account_number ? '• ' . $acc->account_number : '' }}</div>
                        </div>
                    </div>
                    <div class="font-mono text-xs font-bold text-slate-900">
                        Rp {{ number_format($acc->current_balance, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>

            @if($monthlyBurn > 0)
            <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/60 flex items-center justify-between text-xs">
                <span class="font-bold text-amber-900">Beban Langganan Bulanan (Burn Rate):</span>
                <span class="font-mono font-bold text-amber-900">± Rp {{ number_format($monthlyBurn, 0, ',', '.') }}/bln</span>
            </div>
            @endif
        </div>

    </div>

    <!-- IN-PAGE INVOICE MODAL PREVIEW (VIEW TANPA BUKA TAB BARU) -->
    @if($isInvoiceModalOpen && $selectedInvoice)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6" x-cloak>
        <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col border border-slate-200">
            
            <!-- Modal Header Toolbar -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#C6F24D] text-slate-950 flex items-center justify-center font-bold text-xs">
                        <x-icon name="receipt" class="w-4 h-4" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-[#C6F24D]">Detail Invoice Penagihan</h4>
                        <p class="text-xs font-mono text-slate-300">{{ $selectedInvoice->invoice_number }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- WhatsApp Button -->
                    <a href="{{ $selectedInvoice->whatsapp_share_url }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center gap-1">
                        <x-icon name="send" class="w-3.5 h-3.5" />
                        <span>Kirim WA</span>
                    </a>

                    <!-- Print Button -->
                    <a href="{{ route('invoices.show', $selectedInvoice->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all flex items-center gap-1">
                        <x-icon name="printer" class="w-3.5 h-3.5" />
                        <span>Cetak</span>
                    </a>

                    <!-- Close Modal -->
                    <button wire:click="closeInvoiceModal" class="p-1.5 text-slate-400 hover:text-white rounded-xl transition-colors cursor-pointer">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Modal Body (Invoice Rendered Content) -->
            <div class="p-6 sm:p-8 overflow-y-auto space-y-6 text-slate-900">
                
                <!-- Invoice Top Bar -->
                <div class="flex justify-between items-start border-b border-slate-100 pb-5">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-400">Digital Creative & Freelance Services</p>
                        <p class="text-xs font-mono text-slate-400">{{ $user->email }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-black font-mono tracking-wider text-slate-950">INVOICE</div>
                        <div class="text-xs font-mono text-slate-500 font-bold">{{ $selectedInvoice->invoice_number }}</div>
                        <div class="mt-1">
                            @if($selectedInvoice->status === 'paid')
                                <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold font-mono bg-emerald-100 text-emerald-800">
                                    • LUNAS (PAID)
                                </span>
                            @elseif($selectedInvoice->is_overdue)
                                <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold font-mono bg-rose-100 text-rose-800">
                                    • JATUH TEMPO (OVERDUE)
                                </span>
                            @else
                                <span class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold font-mono bg-amber-100 text-amber-800">
                                    • MENUNGGU PEMBAYARAN
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info 3 Col -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                    <div>
                        <span class="text-[10px] font-bold font-mono uppercase text-slate-400 block mb-1">Ditagihkan Kepada:</span>
                        <strong class="text-slate-900 text-sm block">{{ $selectedInvoice->project->client->name ?? 'Klien' }}</strong>
                        <p class="text-slate-500">{{ $selectedInvoice->project->client?->company ?? '-' }}</p>
                        <p class="text-slate-500">{{ $selectedInvoice->project->client?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold font-mono uppercase text-slate-400 block mb-1">Tanggal Terbit:</span>
                        <strong class="text-slate-900 text-sm block">{{ $selectedInvoice->issue_date ? $selectedInvoice->issue_date->format('d M Y') : '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold font-mono uppercase text-slate-400 block mb-1">Project & Jatuh Tempo:</span>
                        <strong class="text-slate-900 text-sm block">{{ $selectedInvoice->project->name }}</strong>
                        <p class="text-slate-500">{{ ucwords(str_replace('_', ' ', $selectedInvoice->project->category)) }}</p>
                        <span class="text-slate-700 font-bold mt-1 block">Jatuh Tempo: {{ $selectedInvoice->due_date ? $selectedInvoice->due_date->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="border-t-2 border-b-2 border-slate-950 py-2">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-[10px] font-mono uppercase text-slate-400 border-b border-slate-200">
                                <th class="py-2 text-left">NO</th>
                                <th class="py-2 text-left">DESKRIPSI ITEM / JASA</th>
                                <th class="py-2 text-center">QTY</th>
                                <th class="py-2 text-right">HARGA</th>
                                <th class="py-2 text-right">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3 font-mono text-slate-400">01</td>
                                <td class="py-3">
                                    <strong class="block text-slate-900">{{ $selectedInvoice->project->name }}</strong>
                                    <span class="text-[11px] text-slate-500">{{ $selectedInvoice->notes ?? 'Penagihan jasa & pengerjaan deliverable project' }}</span>
                                </td>
                                <td class="py-3 text-center font-mono">1x</td>
                                <td class="py-3 text-right font-mono font-bold">Rp {{ number_format($selectedInvoice->amount, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-950">Rp {{ number_format($selectedInvoice->amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Total Box & Payment Methods -->
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="w-full sm:w-1/2 space-y-2">
                        <span class="text-[10px] font-bold font-mono uppercase text-slate-400 block">Metode Pembayaran:</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                            <div class="flex justify-between font-bold text-slate-900 mb-0.5">
                                <span>Rekening Pembayaran</span>
                                <span class="font-mono text-[11px] text-slate-400">a.n {{ $user->name }}</span>
                            </div>
                            <div class="font-mono font-bold text-slate-800">
                                {{ $accounts->first()?->name ?? 'Bank Transfer' }} ({{ $accounts->first()?->account_number ?? '-' }})
                            </div>
                        </div>
                    </div>

                    <div class="w-full sm:w-1/2 bg-slate-950 text-white rounded-2xl p-5 text-right">
                        <span class="text-[10px] font-bold font-mono uppercase tracking-wider text-slate-400 block mb-1">Total Tagihan (Grand Total)</span>
                        <div class="text-2xl sm:text-3xl font-extrabold font-mono text-[#a3e635]">
                            Rp {{ number_format($selectedInvoice->amount, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] font-mono text-slate-300 mt-1">
                            Status: {{ $selectedInvoice->status === 'paid' ? 'Lunas' : 'Belum Terbayar' }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-between items-center text-xs">
                <span class="text-slate-400">Tekan tombol tutup atau klik di luar untuk kembali.</span>
                <button wire:click="closeInvoiceModal" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold transition-all cursor-pointer">
                    Tutup Preview
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
