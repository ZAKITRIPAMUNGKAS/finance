<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\PurchaseWishlist;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiFinancialAdvisorService
{
    /**
     * Get Complete Financial Health Snapshot for a User.
     */
    public function getSnapshot(int $userId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Total Balance across active accounts
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();
        $totalBalance = (float) $accounts->sum('current_balance');
        $bankBalance = (float) $accounts->where('type', 'bank')->sum('current_balance');
        $ewalletBalance = (float) $accounts->where('type', 'ewallet')->sum('current_balance');
        $cashBalance = (float) $accounts->where('type', 'cash')->sum('current_balance');

        // 2. This Month Incomes & Expenses
        $monthIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netCashflow = $monthIncome - $monthExpense;
        $savingsRate = $monthIncome > 0 ? round(($netCashflow / $monthIncome) * 100, 1) : 0;

        // 3. Subscriptions & Fixed Burn Rate
        $subs = Subscription::where('user_id', $userId)->where('status', 'active')->get();
        $monthlyBurnRate = (float) $subs->sum(fn($s) => $s->monthly_equivalent);
        if ($monthlyBurnRate <= 0) {
            $monthlyBurnRate = max(1000000, $monthExpense > 0 ? $monthExpense : 2500000);
        }

        // 4. Cash Runway in Months
        $runwayMonths = $monthlyBurnRate > 0 ? round($totalBalance / $monthlyBurnRate, 1) : 0;

        // 5. Invoices & Receivables
        $unpaidInvoices = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['sent', 'overdue'])
            ->with(['project.client'])
            ->get();
        $totalReceivables = (float) $unpaidInvoices->sum('amount');
        $overdueCount = $unpaidInvoices->filter(fn($i) => $i->is_overdue || ($i->due_date && Carbon::parse($i->due_date)->lt(Carbon::today())))->count();

        // 6. Top Spending Categories
        $topCategories = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, sum(amount) as total_spent')
            ->groupBy('category_id')
            ->orderByDesc('total_spent')
            ->with('category')
            ->take(4)
            ->get()
            ->map(fn($t) => [
                'name' => $t->category?->name ?? 'Pengeluaran Lainnya',
                'amount' => (float) $t->total_spent,
            ]);

        // 7. Wishlists
        $wishlists = PurchaseWishlist::where('user_id', $userId)->where('is_purchased', false)->get();
        $wishlistTotal = (float) $wishlists->sum('price');

        // 8. Available Money estimation
        $emergencyBuffer = $monthlyBurnRate;
        $availableMoney = max(0, $totalBalance - $emergencyBuffer);

        return [
            'total_balance' => $totalBalance,
            'bank_balance' => $bankBalance,
            'ewallet_balance' => $ewalletBalance,
            'cash_balance' => $cashBalance,
            'month_income' => $monthIncome,
            'month_expense' => $monthExpense,
            'net_cashflow' => $netCashflow,
            'savings_rate' => $savingsRate,
            'monthly_burn_rate' => $monthlyBurnRate,
            'runway_months' => $runwayMonths,
            'total_receivables' => $totalReceivables,
            'overdue_count' => $overdueCount,
            'top_categories' => $topCategories,
            'wishlist_count' => $wishlists->count(),
            'wishlist_total' => $wishlistTotal,
            'available_money' => $availableMoney,
            'accounts_summary' => $accounts->map(fn($a) => "{$a->name} ({$a->type}): Rp " . number_format($a->current_balance, 0, ',', '.'))->implode(', '),
        ];
    }

    /**
     * Ask Financial Copilot a natural language question.
     * Integrates Google Gemini LLM when available, falling back to local expert engine.
     */
    public function ask(int $userId, string $question, array $history = []): array
    {
        $snap = $this->getSnapshot($userId);
        $apiKey = env('GEMINI_API_KEY') ?: config('services.gemini.api_key');

        if (!empty($apiKey)) {
            $geminiResponse = $this->callGemini($apiKey, $snap, $question, $history);
            if ($geminiResponse) {
                return $geminiResponse;
            }
        }

        // Fallback: Local Financial Reasoning Engine
        return $this->evaluateLocally($snap, $question);
    }

    /**
     * Call Google Gemini API with rich financial telemetry.
     */
    private function callGemini(string $apiKey, array $snap, string $question, array $history = []): ?array
    {
        try {
            $systemPrompt = "Anda adalah PortoFinance AI Copilot, asisten keuangan cerdas tingkat dunia untuk freelancer dan kreator digital Indonesia.
Analisis data keuangan pengguna berikut ini secara real-time:
- Total Saldo Likuid Kas: Rp " . number_format($snap['total_balance'], 0, ',', '.') . " (Bank: Rp " . number_format($snap['bank_balance'], 0, ',', '.') . ", E-Wallet: Rp " . number_format($snap['ewallet_balance'], 0, ',', '.') . ")
- Uang Bebas (Available Money): Rp " . number_format($snap['available_money'], 0, ',', '.') . "
- Fixed Monthly Burn Rate: Rp " . number_format($snap['monthly_burn_rate'], 0, ',', '.') . "/bulan
- Cash Runway Kas: {$snap['runway_months']} Bulan
- Pemasukan Bulan Ini: Rp " . number_format($snap['month_income'], 0, ',', '.') . "
- Pengeluaran Bulan Ini: Rp " . number_format($snap['month_expense'], 0, ',', '.') . "
- Net Cashflow / Tabungan: Rp " . number_format($snap['net_cashflow'], 0, ',', '.') . " ({$snap['savings_rate']}%)
- Total Piutang Invoice Klien: Rp " . number_format($snap['total_receivables'], 0, ',', '.') . " ({$snap['overdue_count']} invoice overdue)
- Total Wishlist Tertunda: {$snap['wishlist_count']} barang (Rp " . number_format($snap['wishlist_total'], 0, ',', '.') . ")

Instruksi Format Output:
Berikan respons dalam format JSON dengan kunci:
- verdict: (string pendek misal: 'AMAN & DIREKOMENDASIKAN', 'PERINGATAN RUNWAY', 'ANALISIS CASHFLOW', 'STATUS PIUTANG')
- verdict_type: ('safe' jika kondisi aman, 'warning' jika perlu waspada, 'danger' jika kritis, 'info' jika analisis umum)
- title: (judul ringkas yang tajam)
- message: (penjelasan lengkap, ramah, berbasis data riil pengguna, gunakan angka rupiah yang jelas)
- recommendation: (1-2 kalimat tindakan konkret yang harus dilakukan sekarang)
- metrics: (object key-value dengan 3-4 indikator terpenting)
- action_type: (opsional: 'invoice', 'expense', 'wishlist', 'report', 'budget')
- action_label: (opsional: teks tombol aksi cepat)";

            $messages = [
                ['role' => 'user', 'parts' => [['text' => "System Instructions:\n" . $systemPrompt . "\n\nPertanyaan Pengguna: " . $question]]]
            ];

            $response = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => $messages,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $rawJson = $response->json('candidates.0.content.parts.0.text');
                $data = json_decode($rawJson, true);
                if ($data && isset($data['verdict'], $data['title'], $data['message'])) {
                    return $data;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed, falling back to local engine: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Local Deterministic Expert Reasoning Engine
     */
    private function evaluateLocally(array $snap, string $question): array
    {
        $lower = strtolower(trim($question));

        // Pattern 1: Check affordability to buy an item
        if (preg_match('/(beli|mampu|afford|ambil|checkout|upgrade|biaya)\s*(.+)?/i', $lower)) {
            preg_match('/(\d+[\d\.,]*)\s*(jt|juta|k|ribu|rb)?/i', $lower, $matches);
            $targetPrice = 0;
            if (!empty($matches[1])) {
                $num = (float) str_replace(['.', ','], '', $matches[1]);
                $unit = strtolower($matches[2] ?? '');
                if (in_array($unit, ['jt', 'juta'])) {
                    $targetPrice = $num < 1000 ? $num * 1000000 : $num;
                } elseif (in_array($unit, ['k', 'rb', 'ribu'])) {
                    $targetPrice = $num < 1000000 ? $num * 1000 : $num;
                } else {
                    $targetPrice = $num;
                }
            }

            if ($targetPrice > 0) {
                return $this->evaluateAffordability($snap, $targetPrice);
            }
        }

        // Pattern 2: Runway & Survival
        if (str_contains($lower, 'runway') || str_contains($lower, 'darurat') || str_contains($lower, 'bertahan') || str_contains($lower, 'habis') || str_contains($lower, 'cadangan')) {
            return $this->evaluateRunway($snap);
        }

        // Pattern 3: Piutang / Invoice / Klien
        if (str_contains($lower, 'piutang') || str_contains($lower, 'invoice') || str_contains($lower, 'klien') || str_contains($lower, 'tagihan') || str_contains($lower, 'bayaran')) {
            return $this->evaluateInvoices($snap);
        }

        // Pattern 4: Pengeluaran / Burn Rate / Langganan
        if (str_contains($lower, 'boros') || str_contains($lower, 'pengeluaran') || str_contains($lower, 'burn') || str_contains($lower, 'langganan') || str_contains($lower, 'biaya') || str_contains($lower, 'pos')) {
            return $this->evaluateExpenses($snap);
        }

        // Pattern 5: Pemasukan / Cashflow / Tabungan
        if (str_contains($lower, 'pemasukan') || str_contains($lower, 'income') || str_contains($lower, 'cuan') || str_contains($lower, 'untung') || str_contains($lower, 'tabung')) {
            return $this->evaluateIncome($snap);
        }

        // Default: Full Comprehensive Financial Diagnosis
        return $this->generateFullDiagnosis($snap);
    }

    private function evaluateAffordability(array $snap, float $price): array
    {
        $fmtPrice = 'Rp ' . number_format($price, 0, ',', '.');
        $avail = $snap['available_money'];
        $fmtAvail = 'Rp ' . number_format($avail, 0, ',', '.');
        $postBalance = $snap['total_balance'] - $price;
        $postRunway = $snap['monthly_burn_rate'] > 0 ? round($postBalance / $snap['monthly_burn_rate'], 1) : 0;

        if ($avail >= $price && $postRunway >= 3.0) {
            return [
                'verdict' => 'AMAN & DIREKOMENDASIKAN',
                'verdict_type' => 'safe',
                'title' => "Pembelian {$fmtPrice} Sangat Aman",
                'message' => "Uang bebas (*Available Money*) Anda saat ini adalah **{$fmtAvail}**. Setelah membeli barang ini, sisa kas Anda masih cukup untuk menopang **{$postRunway} bulan runway**, di atas batas aman ideal 3 bulan.",
                'recommendation' => "Anda bisa langsung mengeksekusi pembelian ini tanpa mengganggu dana darurat dan biaya operasional rutin.",
                'metrics' => [
                    'Harga Barang' => $fmtPrice,
                    'Uang Bebas Tersedia' => $fmtAvail,
                    'Sisa Runway Kas' => "{$postRunway} Bulan (Aman)",
                ],
                'action_type' => 'wishlist',
                'action_label' => 'Buka Purchase Wishlist',
            ];
        }

        if ($snap['total_balance'] >= $price) {
            return [
                'verdict' => 'HATI-HATI / PERTIMBANGKAN LAGI',
                'verdict_type' => 'warning',
                'title' => "Bisa Dibeli, Tapi Mengikis Dana Darurat",
                'message' => "Total saldo rekening Anda mencukupi, namun uang bebas ideal Anda hanya **{$fmtAvail}**. Jika membeli sekarang seharga {$fmtPrice}, runway kas Anda akan turun menjadi **{$postRunway} bulan** (di bawah standar ideal 3 bulan).",
                'recommendation' => "Disarankan memasukkannya ke menu **Purchase Wishlist** terlebih dahulu atau tunggu pembayaran invoice klien senilai Rp " . number_format($snap['total_receivables'], 0, ',', '.') . " cair.",
                'metrics' => [
                    'Harga Barang' => $fmtPrice,
                    'Uang Bebas Tersedia' => $fmtAvail,
                    'Runway Pasca Beli' => "{$postRunway} Bulan (Kritis)",
                ],
                'action_type' => 'wishlist',
                'action_label' => 'Simpan ke Wishlist Dulu',
            ];
        }

        return [
            'verdict' => 'TIDAK DIREKOMENDASIKAN SAAT INI',
            'verdict_type' => 'danger',
            'title' => "Saldo Belum Mencukupi",
            'message' => "Total saldo seluruh rekening Anda (Rp " . number_format($snap['total_balance'], 0, ',', '.') . ") belum mencukupi untuk pembelian {$fmtPrice}.",
            'recommendation' => "Fokuskan pada percepatan penagihan invoice piutang (Rp " . number_format($snap['total_receivables'], 0, ',', '.') . ") dan tingkatkan tabungan alokasi 20% bulan ini.",
            'metrics' => [
                'Harga Barang' => $fmtPrice,
                'Total Saldo Kas' => 'Rp ' . number_format($snap['total_balance'], 0, ',', '.'),
                'Kekurangan Dana' => 'Rp ' . number_format($price - $snap['total_balance'], 0, ',', '.'),
            ],
            'action_type' => 'invoice',
            'action_label' => 'Tagih Piutang Klien',
        ];
    }

    private function evaluateRunway(array $snap): array
    {
        $runway = $snap['runway_months'];
        $fmtBurn = 'Rp ' . number_format($snap['monthly_burn_rate'], 0, ',', '.');
        $fmtTotal = 'Rp ' . number_format($snap['total_balance'], 0, ',', '.');

        $status = $runway >= 6 ? 'Sangat Sehat (6+ Bulan)' : ($runway >= 3 ? 'Aman (3–6 Bulan)' : 'Kritis (<3 Bulan)');
        $verdictType = $runway >= 6 ? 'safe' : ($runway >= 3 ? 'warning' : 'danger');

        return [
            'verdict' => $status,
            'verdict_type' => $verdictType,
            'title' => "Cash Runway Anda: {$runway} Bulan",
            'message' => "Dengan total saldo kas aktif sebesar **{$fmtTotal}** dan Fixed Burn Rate bulanan sebesar **{$fmtBurn}**, Anda dapat bertahan hidup selama **{$runway} bulan** tanpa pemasukan baru sama sekali.",
            'recommendation' => $runway < 3 
                ? "Prioritaskan menaikkan alokasi tabungan dana darurat ke minimal 3 bulan burn rate dan tagih piutang yang tertunda."
                : "Pertahankan cashflow positif ini! Anda memiliki bantalan finansial yang sangat baik untuk negosiasi proyek bernilai tinggi.",
            'metrics' => [
                'Total Saldo Kas' => $fmtTotal,
                'Monthly Burn Rate' => $fmtBurn,
                'Durasi Runway' => "{$runway} Bulan",
            ],
            'action_type' => 'budget',
            'action_label' => 'Atur Alokasi Anggaran',
        ];
    }

    private function evaluateInvoices(array $snap): array
    {
        $totalRec = 'Rp ' . number_format($snap['total_receivables'], 0, ',', '.');
        $overdue = $snap['overdue_count'];

        return [
            'verdict' => $overdue > 0 ? 'PERLU TINDAKAN PENAGIHAN' : 'STATUS PIUTANG LANCAR',
            'verdict_type' => $overdue > 0 ? 'warning' : 'safe',
            'title' => "Total Piutang Belum Cair: {$totalRec}",
            'message' => "Anda memiliki **{$totalRec}** invoice aktif dari klien. " . ($overdue > 0 ? "Terdapat **{$overdue} invoice yang telah melewati tanggal jatuh tempo**." : "Seluruh invoice saat ini masih dalam batas waktu pembayaran normal."),
            'recommendation' => $overdue > 0 
                ? "Gunakan fitur **1-Click WhatsApp Follow-up** di menu Clients & Invoices untuk mengirim pengingat sopan ke klien."
                : "Semua invoice berjalan lancar. Pantau tanggal jatuh tempo berkala di halaman Clients.",
            'metrics' => [
                'Total Piutang' => $totalRec,
                'Invoice Overdue' => "{$overdue} Tagihan",
            ],
            'action_type' => 'invoice',
            'action_label' => 'Buka Penagihan WhatsApp',
        ];
    }

    private function evaluateExpenses(array $snap): array
    {
        $fmtExpense = 'Rp ' . number_format($snap['month_expense'], 0, ',', '.');
        $fmtBurn = 'Rp ' . number_format($snap['monthly_burn_rate'], 0, ',', '.');
        $topList = collect($snap['top_categories'])->map(fn($c) => "• **{$c['name']}**: Rp " . number_format($c['amount'], 0, ',', '.'))->implode("\n");

        return [
            'verdict' => 'ANALISIS PENGELUARAN BULAN INI',
            'verdict_type' => 'info',
            'title' => "Pengeluaran Berjalan: {$fmtExpense}",
            'message' => "Fixed Burn Rate langganan rutin Anda adalah **{$fmtBurn}/bulan**.\n\n**Rincian Pengeluaran Terbesar:**\n" . ($topList ?: "Belum ada transaksi pengeluaran tercatat bulan ini."),
            'recommendation' => "Periksa menu **Percentage Budget** untuk memastikan tidak ada pos kategori yang melampaui persentase batas alokasi 50/30/20.",
            'metrics' => [
                'Total Pengeluaran' => $fmtExpense,
                'Fixed Burn Rate' => $fmtBurn,
                'Savings Rate' => "{$snap['savings_rate']}%",
            ],
            'action_type' => 'budget',
            'action_label' => 'Kelola Pos Anggaran',
        ];
    }

    private function evaluateIncome(array $snap): array
    {
        $fmtIncome = 'Rp ' . number_format($snap['month_income'], 0, ',', '.');
        $fmtNet = 'Rp ' . number_format($snap['net_cashflow'], 0, ',', '.');

        return [
            'verdict' => 'ANALISIS PEMASUKAN & CASHFLOW',
            'verdict_type' => $snap['net_cashflow'] >= 0 ? 'safe' : 'warning',
            'title' => "Pemasukan Bulan Ini: {$fmtIncome}",
            'message' => "Total pendapatan tercatat bulan ini sebesar **{$fmtIncome}** dengan laba bersih kas (*Net Cashflow*) sebesar **{$fmtNet}**.\n\nPersentase tabungan bersih (*Savings Rate*) Anda saat ini adalah **{$snap['savings_rate']}%**.",
            'recommendation' => $snap['savings_rate'] >= 20 
                ? "Bagus sekali! Savings rate Anda berada di atas target ideal 20%." 
                : "Tingkatkan savings rate dengan mengoptimalkan margin proyek freelance dan menekan langganan yang tidak terpakai.",
            'metrics' => [
                'Total Pemasukan' => $fmtIncome,
                'Net Cashflow' => $fmtNet,
                'Savings Rate' => "{$snap['savings_rate']}%",
            ],
            'action_type' => 'report',
            'action_label' => 'Lihat Laporan Arus Kas',
        ];
    }

    private function generateFullDiagnosis(array $snap): array
    {
        $score = 50;
        if ($snap['runway_months'] >= 3) $score += 20;
        if ($snap['runway_months'] >= 6) $score += 10;
        if ($snap['savings_rate'] >= 20) $score += 15;
        if ($snap['overdue_count'] === 0) $score += 5;

        $healthStatus = $score >= 80 ? 'PRIMA & KOKOH' : ($score >= 60 ? 'STABIL / CUKUP BAIK' : 'BUTUH PERHATIAN');
        $verdictType = $score >= 80 ? 'safe' : ($score >= 60 ? 'info' : 'warning');

        return [
            'verdict' => "SKOR KESEHATAN FINANSIAL: {$score}/100 ({$healthStatus})",
            'verdict_type' => $verdictType,
            'title' => "Ringkasan Eksekutif Keuangan Anda",
            'message' => "Kondisi kas Anda mencakup **{$snap['runway_months']} bulan runway** dengan tabungan bersih bulan ini **{$snap['savings_rate']}%**. Uang bebas (*Available Money*) Anda berada di angka **Rp " . number_format($snap['available_money'], 0, ',', '.') . "**.",
            'recommendation' => "Pertahankan kedisiplinan alokasi anggaran 50/30/20 dan pastikan seluruh invoice klien difollow up tepat waktu.",
            'metrics' => [
                'Total Kas Aktif' => 'Rp ' . number_format($snap['total_balance'], 0, ',', '.'),
                'Uang Bebas' => 'Rp ' . number_format($snap['available_money'], 0, ',', '.'),
                'Runway Kas' => "{$snap['runway_months']} Bulan",
                'Piutang Tertahan' => 'Rp ' . number_format($snap['total_receivables'], 0, ',', '.'),
            ],
            'action_type' => 'report',
            'action_label' => 'Cetak Laporan Keuangan',
        ];
    }
}
