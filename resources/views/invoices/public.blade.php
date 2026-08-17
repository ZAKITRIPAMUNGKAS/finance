<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $user->name }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700;800&display=swap" rel="stylesheet">
    
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
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            padding: 36px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Toolbar */
        .top-toolbar {
            width: 100%;
            max-width: 850px;
            margin-bottom: 20px;
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            padding: 9px 16px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: none;
            font-family: inherit;
            white-space: nowrap;
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
            padding: 44px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 60px;
            right: 60px;
            font-size: 60px;
            font-weight: 800;
            color: rgba(34, 197, 94, 0.12);
            transform: rotate(-12deg);
            pointer-events: none;
            letter-spacing: 4px;
            z-index: 0;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
            gap: 20px;
        }

        .company-info h1 {
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            color: var(--text-main);
            letter-spacing: -0.3px;
        }

        .company-info h1 svg {
            width: 26px;
            height: 26px;
            shrink-0: 0;
        }

        .company-info p {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 3px;
        }

        .company-info .email {
            font-family: 'JetBrains Mono', monospace;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 6px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-main);
            line-height: 1;
        }

        .invoice-title p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
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
            border-radius: 14px;
            padding: 22px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
            border: 1px solid #f1f5f9;
        }

        .info-col h3 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .info-col p {
            font-size: 12px;
            line-height: 1.5;
            color: var(--text-muted);
        }

        .info-col strong {
            font-size: 14px;
            display: block;
            margin-bottom: 3px;
            color: var(--text-main);
            font-weight: 700;
        }

        /* Table Responsive Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 520px;
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
            letter-spacing: 0.5px;
        }

        th:last-child {
            text-align: right;
        }

        td {
            padding: 18px 10px;
            font-size: 13px;
            border-bottom: 2px solid var(--text-main);
            vertical-align: top;
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
            width: 44px;
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
            line-height: 1.4;
            display: block;
        }

        .col-qty, .col-price {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Payment & Total */
        .payment-summary-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 32px;
            margin-bottom: 40px;
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
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .bank-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 10px;
            background-color: var(--light-bg);
            transition: border-color 0.15s ease;
        }

        .bank-card:hover {
            border-color: #cbd5e1;
        }

        .bank-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            gap: 8px;
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

        .bank-account-num-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .bank-account-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: 0.5px;
        }

        .btn-copy-acc {
            font-size: 10px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 2px 8px;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-copy-acc:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        .total-box {
            background-color: var(--dark-bg);
            color: var(--white);
            border-radius: 16px;
            padding: 24px 28px;
            width: 320px;
            min-width: 290px;
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        }

        .total-box h3 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .total-box .amount {
            font-family: 'JetBrains Mono', monospace;
            font-size: 30px;
            font-weight: 800;
            color: var(--green-neon);
            margin-bottom: 8px;
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
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px dashed var(--border-color);
            padding-top: 24px;
            position: relative;
            z-index: 1;
            gap: 20px;
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
            text-align: right;
        }

        .signature p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 24px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .signature strong {
            display: inline-block;
            font-size: 13px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 4px;
            margin-bottom: 4px;
            color: var(--text-main);
            min-width: 140px;
        }

        .signature span {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
        }

        /* ═══════════════════════════════════════════════════════════ */
        /* RESPONSIVE BREAKPOINTS (FOR MOBILE & TABLET SMARTPHONES)    */
        /* ═══════════════════════════════════════════════════════════ */
        @media screen and (max-width: 768px) {
            body {
                padding: 16px 12px;
            }

            .top-toolbar {
                padding: 12px 14px;
                border-radius: 14px;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .btn-group {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                width: 100%;
            }

            .btn {
                width: 100%;
                padding: 10px 12px;
                font-size: 11px;
            }

            .invoice-container {
                padding: 22px 16px !important;
                border-radius: 16px !important;
            }

            .watermark {
                font-size: 32px;
                top: 40px;
                right: 16px;
                opacity: 0.1;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                margin-bottom: 22px;
            }

            .invoice-title {
                text-align: left;
                width: 100%;
            }

            .invoice-title h2 {
                font-size: 26px;
            }

            .info-box {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 16px;
                margin-bottom: 24px;
            }

            .info-col:not(:last-child) {
                border-bottom: 1px dashed #e2e8f0;
                padding-bottom: 12px;
            }

            table {
                min-width: 460px;
            }

            th, td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .payment-summary-section {
                flex-direction: column-reverse;
                gap: 20px;
                margin-bottom: 28px;
            }

            .total-box {
                width: 100%;
                min-width: 0;
                padding: 18px 20px;
                text-align: left;
                border-radius: 14px;
            }

            .total-box .status {
                justify-content: flex-start;
            }

            .total-box .amount {
                font-size: 24px;
            }

            .footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
                padding-top: 18px;
            }

            .signature {
                text-align: left;
                width: 100%;
            }

            .signature p {
                margin-bottom: 16px;
            }
        }

        /* ═══════════════════════════════════════════════════════════ */
        /* PRINT MEDIA (A4 CRISP & ACCURATE)                          */
        /* ═══════════════════════════════════════════════════════════ */
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
<body>

    <!-- TOP TOOLBAR (NO-PRINT) -->
    <div class="no-print top-toolbar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background: #0f172a; color: #a3e635; display: flex; align-items: center; justify-content: center; shrink-0: 0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/></svg>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 800; color: #0f172a;">Invoice Resmi</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #64748b; margin-left: 4px; font-weight: 700;">{{ $invoice->invoice_number }}</span>
            </div>
        </div>

        <div class="btn-group">
            <!-- Confirm Payment WA -->
            @php
                $confirmMsg = "Halo {$user->name}, saya ingin konfirmasi pembayaran invoice *{$invoice->invoice_number}* untuk project *{$invoice->project->name}* sebesar *Rp " . number_format($invoice->amount, 0, ',', '.') . "*. Berikut bukti transfernya:";
            @endphp
            <a href="https://wa.me/?text={{ rawurlencode($confirmMsg) }}" target="_blank" class="btn btn-wa">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                <span>Konfirmasi WA</span>
            </a>

            <!-- Print / Save PDF -->
            <button onclick="window.print()" class="btn btn-print">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- INVOICE CONTAINER -->
    <div class="invoice-container">
        @if($invoice->status === 'paid')
        <div class="watermark">PAID</div>
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
                <p>{{ $invoice->invoice_number }}</p>
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
                <h3>DITAGIHKAN KEPADA:</h3>
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
                <h3 style="margin-top: 12px;">JATUH TEMPO:</h3>
                <strong style="{{ $invoice->is_overdue ? 'color: #e11d48;' : '' }}">
                    {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                </strong>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th>DESKRIPSI LAYANAN / ITEM</th>
                        <th style="text-align: center;">QTY</th>
                        <th style="text-align: right;">HARGA</th>
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
                        <td class="col-qty" style="text-align: center;">1x</td>
                        <td class="col-price" style="text-align: right;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
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
                        <div class="bank-account-num-row">
                            <span class="bank-account-num" id="acc-num-{{ $acc->id }}">{{ $acc->account_number ?? 'Hubungi Pengirim' }}</span>
                            @if($acc->account_number)
                            <button type="button" 
                                    class="btn-copy-acc" 
                                    onclick="navigator.clipboard.writeText('{{ $acc->account_number }}'); this.innerText='Tersalin!'; setTimeout(() => this.innerText='Salin', 1500);">
                                Salin
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="bank-card">
                        <div class="bank-card-header">
                            <strong>Rekening Pembayaran</strong>
                            <span>a.n {{ $user->name }}</span>
                        </div>
                        <p class="bank-account-num">Hubungi Pengirim ({{ $user->email }})</p>
                    </div>
                @endif

                @if($invoice->notes)
                <div style="margin-top: 10px; font-size: 11px; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; line-height: 1.4;">
                    <strong style="color: #0f172a;">Catatan Tambahan:</strong> {{ $invoice->notes }}
                </div>
                @endif
            </div>

            <div class="total-box">
                <h3>TOTAL TAGIHAN (GRAND TOTAL)</h3>
                <div class="amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                @if($invoice->status === 'paid')
                <div class="status">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a3e635" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Lunas pada {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y') : '-' }}</span>
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
                <span>Invoice resmi ini diterbitkan secara otomatis via PortoFinance.</span>
            </div>
            <div class="signature">
                <p>HORMAT KAMI,</p>
                <strong>{{ $user->name }}</strong>
                <span>Freelancer / Studio Kreatif</span>
            </div>
        </div>
    </div>

</body>
</html>
