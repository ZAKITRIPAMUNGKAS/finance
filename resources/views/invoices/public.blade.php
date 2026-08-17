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
    
    <!-- Vite Assets for Icons & Alpine -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            padding: 30px 16px;
            min-height: 100vh;
        }

        .action-toolbar {
            max-width: 850px;
            margin: 0 auto 24px auto;
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .action-btn {
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
        }

        .action-btn-wa {
            background-color: #10b981;
            color: #ffffff;
        }
        .action-btn-wa:hover {
            background-color: #059669;
        }

        .action-btn-print {
            background-color: #0f172a;
            color: var(--green-neon);
        }
        .action-btn-print:hover {
            background-color: #1e293b;
        }

        .invoice-wrapper {
            display: flex;
            justify-content: center;
        }

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
            top: 80px;
            right: 100px;
            font-size: 72px;
            font-weight: 800;
            color: rgba(34, 197, 94, 0.15);
            transform: rotate(-15deg);
            pointer-events: none;
            letter-spacing: 5px;
            z-index: 0;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
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
            width: 26px;
            height: 26px;
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
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 6px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-main);
        }

        .invoice-title p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-main);
            margin-bottom: 8px;
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
            text-transform: uppercase;
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
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
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
            display: flex;
            justify-content: space-between;
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
            margin-bottom: 10px;
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
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
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
            text-align: right;
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
            display: block;
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
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 16px 8px;
            }
            .invoice-container {
                padding: 24px 18px;
            }
            .header, .payment-summary-section, .footer {
                flex-direction: column;
                gap: 24px;
            }
            .info-box {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .invoice-title, .total-box, .signature {
                text-align: left;
            }
            .total-box {
                width: 100%;
                align-items: flex-start;
            }
            .total-box .status {
                justify-content: flex-start;
            }
            .watermark {
                font-size: 44px;
                right: 20px;
                top: 140px;
            }
            .table-container {
                overflow-x: auto;
            }
            table {
                min-width: 520px;
            }
        }

        /* Print Mode */
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
<body x-data="{ copiedAcc: null }">

    <!-- ACTION TOOLBAR (NO-PRINT) -->
    <div class="no-print action-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 28px; height: 28px; border-radius: 8px; background: #0f172a; color: #a3e635; display: flex; align-items: center; justify-content: center;">
                <x-icon name="receipt" style="width: 16px; height: 16px;" />
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 800; color: #0f172a; display: block;">Invoice Resmi</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: #64748b;">{{ $invoice->invoice_number }}</span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <!-- Confirm Payment WA -->
            @php
                $confirmMsg = "Halo {$user->name}, saya ingin konfirmasi pembayaran invoice *{$invoice->invoice_number}* untuk project *{$invoice->project->name}* sebesar *Rp " . number_format($invoice->amount, 0, ',', '.') . "*. Berikut bukti transfernya:";
            @endphp
            <a href="https://wa.me/?text={{ urlencode($confirmMsg) }}" target="_blank" class="action-btn action-btn-wa">
                <x-icon name="send" style="width: 14px; height: 14px;" />
                <span>Konfirmasi Pembayaran</span>
            </a>

            <!-- Print / Save PDF -->
            <button onclick="window.print()" class="action-btn action-btn-print">
                <x-icon name="file-text" style="width: 14px; height: 14px;" />
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- INVOICE SHEET -->
    <div class="invoice-wrapper">
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
                    @if($invoice->project->client?->email)
                    <p class="email" style="font-size: 11px;">{{ $invoice->project->client->email }}</p>
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
                        <div class="bank-card" style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <div style="flex: 1;">
                                <div class="bank-card-header">
                                    <strong>{{ $acc->name }} ({{ strtoupper($acc->type) }})</strong>
                                    <span>a.n {{ $user->name }}</span>
                                </div>
                                <p>{{ $acc->account_number ?? 'Hubungi Pengirim' }}</p>
                            </div>
                            @if($acc->account_number)
                            <button type="button" 
                                @click="navigator.clipboard.writeText('{{ $acc->account_number }}'); copiedAcc = {{ $acc->id }}; setTimeout(() => copiedAcc = null, 2000)"
                                class="no-print"
                                style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 700; padding: 6px 12px; background: #e2e8f0; border: none; border-radius: 8px; cursor: pointer; color: #1e293b;">
                                <span x-text="copiedAcc === {{ $acc->id }} ? '✓ Tersalin' : 'Salin'">Salin</span>
                            </button>
                            @endif
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
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a3e635" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
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
    </div>

</body>
</html>
