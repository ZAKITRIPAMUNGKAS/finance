<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan — {{ $startDate->translatedFormat('F Y') }} — PortoFinance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 11pt; }
            .print-page { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
        }
        @page {
            margin: 1.5cm;
            size: A4 portrait;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased min-h-screen py-6 sm:py-10">

    <!-- TOP TOOLBAR (NO-PRINT) -->
    <div class="no-print max-w-4xl mx-auto px-4 mb-6">
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('analytics') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors text-xs font-bold flex items-center gap-1.5">
                    <span>← Kembali ke Aplikasi</span>
                </a>
                <div class="hidden sm:block text-xs text-slate-400">|</div>
                <span class="text-xs font-extrabold text-slate-800">Periode: {{ $startDate->translatedFormat('F Y') }}</span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Change Period -->
                <form action="{{ route('reports.financial-statement') }}" method="GET" class="flex items-center gap-1.5">
                    <select name="month" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-800">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                    <select name="year" class="bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-bold text-slate-800">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold">
                        Filter
                    </button>
                </form>

                <a href="{{ route('reports.export-csv') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold flex items-center gap-1">
                    <span>📥 Unduh CSV</span>
                </a>

                <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-slate-950 text-[#C6F24D] font-extrabold text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition-all">
                    <span>🖨️ Cetak / PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- DOCUMENT PAGE CONTAINER (A4 PRINT FORM) -->
    <div class="print-page max-w-4xl mx-auto bg-white border border-slate-200 rounded-3xl p-8 sm:p-12 shadow-md space-y-8">
        
        <!-- Document Header / Letterhead -->
        <div class="flex items-start justify-between border-b-2 border-slate-950 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black text-sm">
                        PF
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-950">PortoFinance</span>
                </div>
                <h1 class="text-2xl font-black text-slate-950 uppercase tracking-tight pt-2">Laporan Arus Kas & Laba Rugi</h1>
                <p class="text-xs text-slate-500 font-medium">Laporan Rekapitulasi Finansial Freelance & Personal</p>
            </div>

            <div class="text-right text-xs space-y-1">
                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                <div class="text-slate-500">{{ $user->email }}</div>
                <div class="font-mono text-slate-400">Periode: <strong class="text-slate-900">{{ $startDate->translatedFormat('d M Y') }} – {{ $endDate->translatedFormat('d M Y') }}</strong></div>
                <div class="text-[10px] text-slate-400">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- 1. EXECUTIVE SUMMARY (KPI TILES) -->
        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Total Pemasukan</span>
                <div class="text-xl font-black font-mono text-emerald-700">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Total Pengeluaran</span>
                <div class="text-xl font-black font-mono text-rose-700">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950 text-white space-y-1">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Laba Bersih (Net Cashflow)</span>
                <div class="text-xl font-black font-mono text-[#C6F24D]">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-slate-300 font-mono">Profit Margin: {{ $profitMargin }}%</span>
            </div>
        </div>

        <!-- 2. REKAP PENGELUARAN PER KATEGORI -->
        <div class="space-y-3">
            <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-200 pb-2">
                1. Rincian Pengeluaran Berdasarkan Kategori
            </h3>
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-2.5 px-3">Nama Kategori Pos</th>
                        <th class="py-2.5 px-3 text-right">Nominal (Rp)</th>
                        <th class="py-2.5 px-3 text-right">Porsi Pengeluaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($expensesByCategory as $catName => $amount)
                    @php
                        $pct = $totalExpense > 0 ? round(($amount / $totalExpense) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="py-2.5 px-3 font-bold text-slate-900">{{ $catName }}</td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-600">{{ $pct }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-4 text-center text-slate-400">Tidak ada pengeluaran pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-300 font-black bg-slate-50/50">
                        <td class="py-2.5 px-3 uppercase">Total Pengeluaran Operasional</td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-950">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-right font-mono">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 3. POSISI SALDO KAS & REKENING AKTIF -->
        <div class="space-y-3">
            <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-200 pb-2">
                2. Posisi Saldo Kas & Rekening (Liquid Assets)
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($accounts as $acc)
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-0.5">
                    <div class="text-[10px] font-bold uppercase text-slate-500">{{ $acc->name }} ({{ strtoupper($acc->type) }})</div>
                    <div class="font-mono font-black text-sm text-slate-900">Rp {{ number_format($acc->current_balance, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
            <div class="p-3 bg-slate-100 rounded-xl flex items-center justify-between text-xs font-black">
                <span>TOTAL LIKUIDITAS KAS AKTIF:</span>
                <span class="font-mono text-sm text-slate-950">Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 4. REKAP INVOICE KLIEN PADA PERIODE INI -->
        @if($invoices->isNotEmpty())
        <div class="space-y-3">
            <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider border-b border-slate-200 pb-2">
                3. Rekapitulasi Invoice Freelance Terbit
            </h3>
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-bold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-2 px-3">No. Invoice</th>
                        <th class="py-2 px-3">Klien & Proyek</th>
                        <th class="py-2 px-3">Tanggal Terbit</th>
                        <th class="py-2 px-3 text-right">Nominal</th>
                        <th class="py-2 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoices as $inv)
                    <tr>
                        <td class="py-2 px-3 font-mono font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                        <td class="py-2 px-3">{{ $inv->project->name ?? '-' }} ({{ $inv->project->client->name ?? '-' }})</td>
                        <td class="py-2 px-3 font-mono text-slate-600">{{ \Carbon\Carbon::parse($inv->issue_date)->translatedFormat('d M Y') }}</td>
                        <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-center">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Document Footer / Legal Verification -->
        <div class="pt-8 border-t border-slate-200 flex items-end justify-between text-xs">
            <div class="space-y-1 text-slate-400 text-[10px]">
                <p>Dokumen ini dihasilkan secara otomatis oleh sistem pencatatan keuangan PortoFinance.</p>
                <p>Semua data keuangan tercatat secara terenkripsi dan diverifikasi oleh pemilik akun.</p>
            </div>

            <div class="text-center space-y-8">
                <div class="text-[10px] font-bold uppercase text-slate-400">Disusun Oleh,</div>
                <div class="border-b border-slate-400 w-36 pb-1 font-bold text-slate-900">{{ $user->name }}</div>
            </div>
        </div>

    </div>

</body>
</html>
