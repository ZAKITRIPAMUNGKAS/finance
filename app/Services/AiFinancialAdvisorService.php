<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\PurchaseWishlist;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

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
            // Fallback to average monthly expense if no subscription logged
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

        // 6. Top Spending Category
        $topCategories = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category_id, sum(amount) as total_spent')
            ->groupBy('category_id')
            ->orderByDesc('total_spent')
            ->with('category')
            ->take(3)
            ->get()
            ->map(fn($t) => [
                'name' => $t->category?->name ?? 'Pengeluaran Lainnya',
                'amount' => (float) $t->total_spent,
            ]);

        // 7. Wishlists
        $wishlists = PurchaseWishlist::where('user_id', $userId)->where('is_purchased', false)->get();
        $wishlistTotal = (float) $wishlists->sum('price');

        // 8. Available Money estimation (Total Cash - Emergency Buffer (1 Month Burn) - Committed Wishlist)
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
        ];
    }

    /**
     * Ask Financial Copilot a natural language question.
     */
    public function ask(int $userId, string $question): array
    {
        $snap = $this->getSnapshot($userId);
        $lower = strtolower(trim($question));

        // Pattern 1: Check affordability to buy an item (e.g. "bisa beli kamera 10 juta?", "aman beli...")
        if (preg_match('/(beli|mampu|afford|ambil|checkout|upgrade)\s*(.+)?/i', $lower)) {
            // Extract potential nominal number
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

        // Pattern 2: Runway & Survival / Emergency Buffer
        if (str_contains($lower, 'runway') || str_contains($lower, 'darurat') || str_contains($lower, 'bertahan') || str_contains($lower, 'habis')) {
            return $this->evaluateRunway($snap);
        }

        // Pattern 3: Piutang / Invoice / Klien
        if (str_contains($lower, 'piutang') || str_contains($lower, 'invoice') || str_contains($lower, 'klien') || str_contains($lower, 'tagihan')) {
            return $this->evaluateInvoices($snap);
        }

        // Pattern 4: Pengeluaran / Burn Rate / Langganan
        if (str_contains($lower, 'boros') || str_contains($lower, 'pengeluaran') || str_contains($lower, 'burn') || str_contains($lower, 'langganan') || str_contains($lower, 'pos')) {
            return $this->evaluateExpenses($snap);
        }

        // Default: Full Financial Health & Actionable Recommendation
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
                'message' => "Uang bebas (*Available Money*) Anda saat ini adalah {$fmtAvail}. Setelah membeli barang ini, sisa kas Anda masih cukup untuk menopang **{$postRunway} bulan runway**, di atas batas aman ideal 3 bulan.",
                'recommendation' => "Anda bisa langsung mengeksekusi pembelian ini tanpa mengganggu dana darurat dan biaya operasional rutin.",
                'metrics' => [
                    'Harga Barang' => $fmtPrice,
                    'Uang Bebas Tersedia' => $fmtAvail,
                    'Sisa Runway Kas' => "{$postRunway} Bulan (Aman)",
                ]
            ];
        }

        if ($snap['total_balance'] >= $price) {
            return [
                'verdict' => 'HATI-HATI / PERTIMBANGKAN LAGI',
                'verdict_type' => 'warning',
                'title' => "Bisa Dibeli, Tapi Mengikis Dana Darurat",
                'message' => "Total saldo rekening Anda mencukupi, namun uang bebas ideal Anda hanya {$fmtAvail}. Jika membeli sekarang seharga {$fmtPrice}, runway kas Anda akan turun menjadi **{$postRunway} bulan** (di bawah standar ideal 3 bulan).",
                'recommendation' => "Disarankan memasukkannya ke menu **Purchase Wishlist** terlebih dahulu atau tunggu pembayaran invoice klien senilai Rp " . number_format($snap['total_receivables'], 0, ',', '.') . " cair.",
                'metrics' => [
                    'Harga Barang' => $fmtPrice,
                    'Uang Bebas Tersedia' => $fmtAvail,
                    'Runway Pasca Beli' => "{$postRunway} Bulan (Pas-pasan)",
                ]
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
            ]
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
            ]
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
            ]
        ];
    }

    private function evaluateExpenses(array $snap): array
    {
        $fmtExpense = 'Rp ' . number_format($snap['month_expense'], 0, ',', '.');
        $fmtBurn = 'Rp ' . number_format($snap['monthly_burn_rate'], 0, ',', '.');
        $topList = collect($snap['top_categories'])->map(fn($c) => "• {$c['name']}: Rp " . number_format($c['amount'], 0, ',', '.'))->implode("\n");

        return [
            'verdict' => 'ANALISIS PENGELUARAN BULAN INI',
            'verdict_type' => 'info',
            'title' => "Pengeluaran Berjalan: {$fmtExpense}",
            'message' => "Fixed Burn Rate langganan rutin Anda adalah **{$fmtBurn}/bulan**.\n\n**3 Kategori Pengeluaran Terbesar:**\n" . ($topList ?: "Belum ada transaksi pengeluaran tercatat bulan ini."),
            'recommendation' => "Periksa menu **Percentage Budget** untuk memastikan tidak ada pos kategori yang melampaui persentase batas alokasi 50/30/20.",
            'metrics' => [
                'Total Pengeluaran' => $fmtExpense,
                'Fixed Burn Rate' => $fmtBurn,
                'Savings Rate' => "{$snap['savings_rate']}%",
            ]
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
            ]
        ];
    }
}
