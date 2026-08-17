<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $user->name }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Script -->
    @vite(['resources/js/app.js'])

    <style>
        :root {
            --bg-color: #f1f5f9;
            --white: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --light-bg: #f8fafc;
            --dark-bg: #0f172a;
            --green-neon: #a3e635;
            --green-badge: #dcfce7;
            --green-badge-text: #166534;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Top Action Toolbar */
        .top-toolbar {
            width: 100%;
            max-width: 850px;
            margin-bottom: 24px;
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: none;
            font-family: inherit;
        }

        .btn-back {
            background-color: #f1f5f9;
            color: #334155;
        }
        .btn-back:hover {
            background-color: #e2e8f0;
        }

        .btn-copy {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .btn-copy:hover {
            background-color: #f1f5f9;
        }

        .btn-wa {
            background-color: #10b981;
            color: #ffffff;
        }
        .btn-wa:hover {
            background-color: #059669;
        }

        .btn-print {
            background-color: #0f172a;
            color: var(--green-neon);
        }
        .btn-print:hover {
            background-color: #1e293b;
        }

        /* Invoice Container */
        .invoice-container {
            background-color: var(--white);
            width: 100%;
            max-width: 850px;
            padding: 50px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 70px;
            right: 80px;
            font-size: 70px;
            font-weight: 800;
            color: rgba(34, 197, 94, 0.15);
            transform: rotate(-15deg);
            pointer-events: none;
            letter-spacing: 5px;
            z-index: 0;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Header */
        .header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }

        .company-info h1 {
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .company-info h1 svg {
            width: 24px;
            height: 24px;
        }

        .company-info p {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .company-info .email {
            font-family: 'JetBrains Mono', monospace;
            color: #94a3b8;
            font-size: 11px;
        }

        .invoice-title {
            text-align: right !important;
        }

        .invoice-title h2 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 8px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-main);
            line-height: 1;
        }

        .invoice-title p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            font-family: 'JetBrains Mono', monospace;
        }

        .badge-paid {
            background-color: var(--green-badge);
            color: var(--green-badge-text);
        }

        .badge-unpaid {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Info Box */
        .info-box {
            background-color: var(--light-bg);
            border-radius: 12px;
            padding: 25px;
            display: grid !important;
            grid-template-columns: 2fr 1fr 1fr !important;
            gap: 20px;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
            border: 1px solid #f1f5f9;
        }

        .info-col h3 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .info-col p {
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .info-col strong {
            font-size: 15px;
            display: block;
            margin-bottom: 4px;
            color: var(--text-main);
            font-weight: 700;
        }

        /* Table */
        .table-container {
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 10px;
            border-top: 2px solid var(--text-main);
            border-bottom: 2px solid var(--text-main);
            font-weight: 700;
        }

        th:last-child {
            text-align: right;
        }

        td {
            padding: 20px 10px;
            font-size: 13px;
            border-bottom: 2px solid var(--text-main);
        }

        td:last-child {
            text-align: right;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: var(--text-main);
        }

        .col-no {
            font-family: 'JetBrains Mono', monospace;
            color: #94a3b8;
            font-weight: 700;
            width: 50px;
        }

        .item-desc strong {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .item-desc span {
            color: var(--text-muted);
            font-size: 11px;
        }

        .col-qty, .col-price {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Payment & Total */
        .payment-summary-section {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 40px;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .payment-methods {
            flex: 1;
        }

        .payment-methods h3 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .bank-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 12px;
            background-color: var(--light-bg);
        }

        .bank-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .bank-card-header strong {
            font-size: 13px;
            color: var(--text-main);
        }

        .bank-card-header span {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        .bank-card p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
        }

        .total-box {
            background-color: var(--dark-bg);
            color: var(--white);
            border-radius: 12px;
            padding: 25px 30px;
            width: 320px;
            min-width: 300px;
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .total-box h3 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .total-box .amount {
            font-family: 'JetBrains Mono', monospace;
            font-size: 32px;
            font-weight: 800;
            color: var(--green-neon);
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .total-box .status {
            font-size: 11px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Footer */
        .footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-end !important;
            border-top: 1px dashed var(--border-color);
            padding-top: 30px;
            position: relative;
            z-index: 1;
        }

        .footer-note p {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .footer-note span {
            font-size: 11px;
            color: var(--text-muted);
        }

        .signature {
            text-align: right !important;
        }

        .signature p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 30px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .signature strong {
            display: inline-block;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 5px;
            margin-bottom: 5px;
            color: var(--text-main);
            min-width: 140px;
        }

        .signature span {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
        }

        /* Print Media */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                padding: 0 !important;
                color: black !important;
            }
            .invoice-container {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            @page {
                margin: 1.2cm;
                size: A4 portrait;
            }
        }
    </style>
</head>
<body x-data="{ copied: false }">

    <!-- TOP TOOLBAR (NO-PRINT) -->
    <div class="no-print top-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('projects') }}" class="btn btn-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                <span>Kembali ke Projects</span>
            </a>
            <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 700; color: #64748b;">
                {{ $invoice->invoice_number }}
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <!-- Copy Public Link -->
            <button type="button" 
                @click="navigator.clipboard.writeText('{{ $invoice->public_url }}'); copied = true; setTimeout(() => copied = false, 2500)"
                class="btn btn-copy">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                <span x-text="copied ? '✓ Link Tersalin!' : 'Salin Link Klien'">Salin Link Klien</span>
            </button>

            <!-- Share via WhatsApp -->
            <a href="{{ $invoice->whatsapp_share_url }}" target="_blank" class="btn btn-wa">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                <span>Kirim via WhatsApp</span>
            </a>

            <!-- Print / Save PDF -->
            <button onclick="window.print()" class="btn btn-print">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- INVOICE CONTAINER -->
    <div class="invoice-container">
        @if($invoice->status === 'paid')
        <div class="watermark">PAID / LUNAS</div>
        @endif

        <div class="header">
            <div class="company-info">
                <h1>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" rx="6" fill="#0f172a"/>
                        <path d="M7 8H17M7 12H17M7 16H12" stroke="#a3e635" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    {{ $user->name }}
                </h1>
                <p>Digital Creative & Freelance Services</p>
                <p class="email">{{ $user->email }}</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>No: {{ $invoice->invoice_number }}</p>
                @if($invoice->status === 'paid')
                    <div class="badge badge-paid">• LUNAS (PAID)</div>
                @elseif($invoice->is_overdue)
                    <div class="badge badge-overdue">• JATUH TEMPO (OVERDUE)</div>
                @else
                    <div class="badge badge-unpaid">• MENUNGGU PEMBAYARAN</div>
                @endif
            </div>
        </div>

        <div class="info-box">
            <div class="info-col">
                <h3>DITAGIHKAN KEPADA (BILL TO):</h3>
                <strong>{{ $invoice->project->client->name ?? 'Klien' }}</strong>
                @if($invoice->project->client?->company)
                <p>{{ $invoice->project->client->company }}</p>
                @endif
                @if($invoice->project->client?->phone)
                <p>{{ $invoice->project->client->phone }}</p>
                @endif
                @if($invoice->project->client?->address)
                <p style="margin-top: 2px;">{{ $invoice->project->client->address }}</p>
                @endif
            </div>
            <div class="info-col">
                <h3>TANGGAL TERBIT:</h3>
                <strong>{{ $invoice->issue_date ? $invoice->issue_date->format('d M Y') : '-' }}</strong>
            </div>
            <div class="info-col">
                <h3>PROJECT:</h3>
                <strong>{{ $invoice->project->name }}</strong>
                <p>{{ ucwords(str_replace('_', ' ', $invoice->project->category)) }}</p>
                <h3 style="margin-top: 15px;">JATUH TEMPO:</h3>
                <strong style="{{ $invoice->is_overdue ? 'color: #e11d48;' : '' }}">
                    {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                </strong>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th>DESKRIPSI LAYANAN / ITEM</th>
                        <th>QTY</th>
                        <th>HARGA SATUAN</th>
                        <th>SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-no">01</td>
                        <td class="item-desc">
                            <strong>{{ $invoice->project->name }}</strong>
                            <span>Layanan {{ ucwords(str_replace('_', ' ', $invoice->project->category)) }} • {{ $invoice->notes ?? 'Penagihan jasa & pengerjaan deliverable project' }}</span>
                        </td>
                        <td class="col-qty">1x</td>
                        <td class="col-price">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="payment-summary-section">
            <div class="payment-methods">
                <h3>METODE PEMBAYARAN (TRANSFER BANK):</h3>
                
                @if($accounts->count() > 0)
                    @foreach($accounts as $acc)
                    <div class="bank-card">
                        <div class="bank-card-header">
                            <strong>{{ $acc->name }} ({{ strtoupper($acc->type) }})</strong>
                            <span>a.n {{ $user->name }}</span>
                        </div>
                        <p>{{ $acc->account_number ?? 'Hubungi Pengirim' }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="bank-card">
                        <div class="bank-card-header">
                            <strong>Rekening Pembayaran</strong>
                            <span>a.n {{ $user->name }}</span>
                        </div>
                        <p>Hubungi Pengirim ({{ $user->email }})</p>
                    </div>
                @endif

                @if($invoice->notes)
                <div style="margin-top: 12px; font-size: 11px; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
                    <strong>Catatan:</strong> {{ $invoice->notes }}
                </div>
                @endif
            </div>

            <div class="total-box">
                <h3>TOTAL TAGIHAN (GRAND TOTAL)</h3>
                <div class="amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                @if($invoice->status === 'paid')
                <div class="status">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#a3e635" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Lunas dibayar pada {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y') : '-' }}
                </div>
                @else
                <div class="status" style="color: #94a3b8;">
                    Status: Belum Terbayar
                </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <div class="footer-note">
                <p>Terima kasih atas kerja sama dan kepercayaannya.</p>
                <span>Invoice ini dibuat secara otomatis melalui platform PortoFinance.</span>
            </div>
            <div class="signature">
                <p>HORMAT KAMI,</p>
                <strong>{{ $user->name }}</strong>
                <span>Freelancer / Creator</span>
            </div>
        </div>
    </div>

</body>
</html>
