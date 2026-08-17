<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    /**
     * Render Print-Ready & PDF-Ready Financial Statement.
     */
    public function financialStatement(Request $request)
    {
        $userId = auth()->id();
        $user = auth()->user();

        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // 1. Incomes
        $incomeTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'account', 'project'])
            ->get();
        $totalIncome = (float) $incomeTransactions->sum('amount');

        // 2. Expenses by category
        $expenseTransactions = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'account'])
            ->get();
        $totalExpense = (float) $expenseTransactions->sum('amount');

        $expensesByCategory = $expenseTransactions->groupBy(fn($t) => $t->category?->name ?? 'Lain-lain')
            ->map(fn($group) => $group->sum('amount'));

        // 3. Net Profit
        $netProfit = $totalIncome - $totalExpense;
        $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0;

        // 4. Accounts & Liquid Balances
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();
        $totalCash = (float) $accounts->sum('current_balance');

        // 5. Active Subscriptions (Monthly Burn)
        $subscriptions = Subscription::where('user_id', $userId)->where('status', 'active')->get();
        $monthlyBurn = (float) $subscriptions->sum(fn($s) => $s->monthly_equivalent);

        // 6. Invoices summary
        $invoices = Invoice::whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['project.client'])
            ->get();

        return view('reports.financial-statement', compact(
            'user',
            'month',
            'year',
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'profitMargin',
            'expensesByCategory',
            'incomeTransactions',
            'expenseTransactions',
            'accounts',
            'totalCash',
            'monthlyBurn',
            'invoices'
        ));
    }

    /**
     * Export all transactions as CSV.
     */
    public function exportTransactionsCsv(): StreamedResponse
    {
        $userId = auth()->id();
        $transactions = Transaction::where('user_id', $userId)
            ->with(['account', 'category', 'project'])
            ->orderBy('date', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="portofinance-transactions-' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            
            // BOM for UTF-8 Excel support
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, ['Tanggal', 'Tipe', 'Kategori', 'Rekening/Dompet', 'Nominal (Rp)', 'Keterangan/Proyek', 'Bisnis Freelance']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->date ? Carbon::parse($t->date)->format('Y-m-d') : '-',
                    strtoupper($t->type),
                    $t->category?->name ?? 'Umum',
                    $t->account?->name ?? '-',
                    $t->amount,
                    $t->description ?: ($t->notes ?: ($t->project?->name ?: '-')),
                    $t->is_business ? 'YA' : 'TIDAK',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
