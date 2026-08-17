<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} — {{ $invoice->project->name }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: black !important; }
            .invoice-card { box-shadow: none !important; border: 1px solid #E2E8F0 !important; border-radius: 0 !important; margin: 0 !important; max-width: 100% !important; padding: 24px !important; }
            @page { margin: 1cm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased min-h-screen py-6 sm:py-10" x-data="{ copiedAcc: null }">

    <!-- TOP TOOLBAR FOR CLIENT (NO-PRINT) -->
    <div class="no-print max-w-4xl mx-auto px-4 mb-6">
        <div class="p-4 bg-white rounded-3xl border border-slate-200/90 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black">
                    <x-icon name="receipt" class="w-4 h-4" />
                </div>
                <div>
                    <span class="text-xs font-black text-slate-900 block">Invoice Penagihan Resmi</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $invoice->invoice_number }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- WhatsApp Confirm Payment -->
                @php
                    $confirmMsg = "Halo {$user->name}, saya ingin konfirmasi pembayaran invoice *{$invoice->invoice_number}* untuk project *{$invoice->project->name}* sebesar *Rp " . number_format($invoice->amount, 0, ',', '.') . "*. Berikut bukti transfernya:";
                @endphp
                <a href="https://wa.me/?text={{ urlencode($confirmMsg) }}" target="_blank"
                    class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                    <x-icon name="send" class="w-3.5 h-3.5" />
                    <span>Konfirmasi Pembayaran</span>
                </a>

                <!-- Print / Save PDF -->
                <button onclick="window.print()" 
                    class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                    <x-icon name="file-text" class="w-3.5 h-3.5" />
                    <span>Cetak / PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- INVOICE SHEET CONTAINER -->
    <div class="max-w-4xl mx-auto px-4">
        <div class="invoice-card bg-white rounded-3xl border border-slate-200/90 shadow-xl p-8 sm:p-12 space-y-8 relative overflow-hidden">
            
            <!-- WATERMARK STATUS STAMP (PAID ONLY) -->
            @if($invoice->status === 'paid')
            <div class="absolute right-8 top-28 sm:top-20 pointer-events-none opacity-20 rotate-[-12deg] select-none">
                <div class="border-4 border-emerald-600 text-emerald-600 px-6 py-2 rounded-2xl text-4xl sm:text-5xl font-black font-mono tracking-widest uppercase">
                    PAID / LUNAS
                </div>
            </div>
            @endif

            <!-- 1. HEADER & BRANDING -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black">
                            <x-icon name="wallet" class="w-5 h-5" />
                        </div>
                        <span class="text-xl font-black tracking-tight text-slate-950">{{ $user->name }}</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Digital Creative & Freelance Services</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $user->email }}</p>
                </div>

                <div class="sm:text-right space-y-1">
                    <span class="text-3xl sm:text-4xl font-black font-mono text-slate-950 tracking-tight block">INVOICE</span>
                    <div class="font-mono text-xs text-slate-500">
                        No: <strong class="text-slate-900">{{ $invoice->invoice_number }}</strong>
                    </div>
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black font-mono uppercase tracking-wider {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($invoice->is_overdue ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                            ● {{ $invoice->status === 'paid' ? 'LUNAS (PAID)' : ($invoice->is_overdue ? 'JATUH TEMPO (OVERDUE)' : 'MENUNGGU PEMBAYARAN (UNPAID)') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. CLIENT & INVOICE META GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-[#F8F9FA] p-6 rounded-2xl border border-slate-100 text-xs">
                <!-- Bill To -->
                <div class="space-y-1">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">Ditagihkan Kepada (Bill To):</span>
                    <h4 class="text-sm font-extrabold text-slate-900">{{ $invoice->project->client->name ?? 'Klien' }}</h4>
                    @if($invoice->project->client?->company)
                    <p class="font-semibold text-slate-700">{{ $invoice->project->client->company }}</p>
                    @endif
                    @if($invoice->project->client?->email)
                    <p class="text-slate-500 font-mono">{{ $invoice->project->client->email }}</p>
                    @endif
                    @if($invoice->project->client?->phone)
                    <p class="text-slate-500 font-mono">{{ $invoice->project->client->phone }}</p>
                    @endif
                    @if($invoice->project->client?->address)
                    <p class="text-slate-500 mt-1">{{ $invoice->project->client->address }}</p>
                    @endif
                </div>

                <!-- Invoice Meta -->
                <div class="sm:text-right space-y-2">
                    <div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Project:</span>
                        <strong class="text-slate-900 font-extrabold text-xs block">{{ $invoice->project->name }}</strong>
                        <span class="text-[11px] text-slate-500">{{ ucwords(str_replace('_', ' ', $invoice->project->category)) }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 sm:border-0 sm:pt-0">
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Tanggal Terbit:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $invoice->issue_date ? $invoice->issue_date->format('d M Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Jatuh Tempo:</span>
                            <span class="font-mono font-bold {{ $invoice->is_overdue ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. ITEM TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b-2 border-slate-900 text-slate-900 font-mono text-[10px] uppercase tracking-wider">
                            <th class="py-3 px-2">No</th>
                            <th class="py-3 px-2">Deskripsi Layanan / Item</th>
                            <th class="py-3 px-2 text-center">Qty</th>
                            <th class="py-3 px-2 text-right">Harga Satuan</th>
                            <th class="py-3 px-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-4 px-2 font-mono text-slate-400">01</td>
                            <td class="py-4 px-2">
                                <strong class="text-slate-900 font-extrabold text-sm block">{{ $invoice->project->name }}</strong>
                                <span class="text-slate-500 text-[11px]">
                                    Layanan {{ ucwords(str_replace('_', ' ', $invoice->project->category)) }} &bull; {{ $invoice->notes ?? 'Penagihan jasa & pengerjaan deliverable project' }}
                                </span>
                            </td>
                            <td class="py-4 px-2 text-center font-mono font-bold text-slate-700">1x</td>
                            <td class="py-4 px-2 text-right font-mono font-bold text-slate-700">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-2 text-right font-mono font-black text-slate-950 text-sm">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. TOTAL SUMMARY & INSTRUCTIONS -->
            <div class="pt-4 border-t-2 border-slate-900 flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                <!-- Payment Instructions -->
                <div class="space-y-2.5 max-w-sm">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Metode Pembayaran (Transfer Bank):</span>
                    
                    @if($accounts->count() > 0)
                    <div class="space-y-2">
                        @foreach($accounts as $acc)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-slate-900">{{ $acc->name }} ({{ strtoupper($acc->type) }})</span>
                                <span class="text-[10px] text-slate-400 font-mono">a.n {{ $user->name }}</span>
                            </div>
                            <div class="font-mono font-black text-sm text-slate-900 mt-1 flex items-center justify-between gap-2">
                                <span>{{ $acc->account_number ?? 'Hubungi Pengirim' }}</span>
                                @if($acc->account_number)
                                <button type="button" 
                                    @click="navigator.clipboard.writeText('{{ $acc->account_number }}'); copiedAcc = {{ $acc->id }}; setTimeout(() => copiedAcc = null, 2000)"
                                    class="no-print text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 transition-colors cursor-pointer">
                                    <span x-text="copiedAcc === {{ $acc->id }} ? '✓ Tersalin' : 'Salin'">Salin</span>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        Silakan hubungi {{ $user->name }} ({{ $user->email }}) untuk konfirmasi rekening pembayaran.
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="mt-3 p-3 rounded-2xl bg-amber-50/60 border border-amber-200/60 text-[11px] text-amber-900">
                        <strong>Catatan:</strong> {{ $invoice->notes }}
                    </div>
                    @endif
                </div>

                <!-- Grand Total Box -->
                <div class="sm:text-right space-y-2 sm:min-w-[240px]">
                    <div class="p-4 bg-slate-950 text-white rounded-2xl space-y-1">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 block">Total Tagihan (Grand Total)</span>
                        <span class="text-2xl sm:text-3xl font-black font-mono text-[#C6F24D] block">
                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                        </span>
                        @if($invoice->status === 'paid')
                        <span class="text-[11px] text-emerald-400 font-bold block pt-1 border-t border-slate-800">
                            ✓ Lunas dibayar pada {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y') : '-' }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 5. SIGNATURE & FOOTER -->
            <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row sm:items-end justify-between gap-6 text-xs text-slate-400">
                <div class="space-y-1">
                    <p class="font-semibold text-slate-700">Terima kasih atas kerja sama dan kepercayaannya.</p>
                    <p class="text-[10px]">Invoice ini dibuat secara otomatis melalui platform PortoFinance.</p>
                </div>

                <div class="sm:text-right space-y-12">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 block">Hormat Kami,</span>
                    <div>
                        <strong class="text-slate-900 font-extrabold text-sm block border-b border-slate-300 pb-1 inline-block min-w-[140px]">{{ $user->name }}</strong>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Freelancer / Creator</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
