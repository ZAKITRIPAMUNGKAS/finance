<?php

namespace App\Services;

use App\Models\BudgetCategory;
use App\Models\BudgetGroup;
use App\Models\BudgetProfile;
use App\Models\Category;
use App\Models\IncomeFloorSnapshot;
use App\Models\PurchaseWishlist;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BudgetAllocationService
{
    /**
     * Menghitung Income Floor P25, Mean, StdDev, dan CV (Coefficient of Variation)
     * Menggunakan data rolling 6-12 bulan.
     */
    public function calculateIncomeFloor(?int $userId = null, int $lookbackMonths = 12): array
    {
        $userId = $userId ?? auth()->id() ?? User::first()?->id;
        
        // Ambil data pemasukan per bulan untuk 12 bulan terakhir
        $monthlyIncomes = [];
        for ($i = $lookbackMonths; $i >= 1; $i--) {
            $mStart = Carbon::now()->subMonths($i)->startOfMonth();
            $mEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $income = (float) Transaction::where('type', 'income')
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->whereBetween('date', [$mStart, $mEnd])
                ->sum('amount');

            if ($income > 0) {
                $monthlyIncomes[] = $income;
            }
        }

        // Fallback jika data historis sedikit (misal akun baru)
        if (count($monthlyIncomes) < 3) {
            $driver = DB::connection()->getDriverName();
            $dateFormat = $driver === 'sqlite' ? "strftime('%Y-%m', date)" : "DATE_FORMAT(date, '%Y-%m')";

            $allMonthly = Transaction::where('type', 'income')
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->select(DB::raw("{$dateFormat} as m"), DB::raw("SUM(amount) as total"))
                ->groupBy('m')
                ->pluck('total')
                ->map(fn($v) => (float)$v)
                ->toArray();

            if (!empty($allMonthly)) {
                $monthlyIncomes = $allMonthly;
            } else {
                $monthlyIncomes = [10000000.0]; // Default starter baseline
            }
        }

        sort($monthlyIncomes);
        $count = count($monthlyIncomes);

        // 1. Hitung Persentil ke-25 (P25) dengan interpolasi linear
        if ($count === 1) {
            $p25 = $monthlyIncomes[0];
        } else {
            $index = 0.25 * ($count - 1);
            $lowerIdx = (int) floor($index);
            $upperIdx = (int) ceil($index);
            $fraction = $index - $lowerIdx;

            $p25 = $monthlyIncomes[$lowerIdx] + ($fraction * ($monthlyIncomes[$upperIdx] - $monthlyIncomes[$lowerIdx]));
        }

        // 2. Hitung Mean (mu)
        $mean = array_sum($monthlyIncomes) / $count;

        // 3. Hitung Standar Deviasi (sigma)
        $varianceSum = 0.0;
        foreach ($monthlyIncomes as $val) {
            $varianceSum += pow($val - $mean, 2);
        }
        $stdDev = $count > 1 ? sqrt($varianceSum / ($count - 1)) : 0.0;

        // 4. Hitung Coefficient of Variation (CV = sigma / mu)
        $cv = $mean > 0 ? ($stdDev / $mean) : 0.0;

        // 5. Tentukan Metode (CV >= 0.3 => Floor Method; CV < 0.3 => Average Method)
        $suggestedMethod = $cv >= 0.3 ? 'floor' : 'average';

        return [
            'income_floor' => round($p25),
            'avg_income' => round($mean),
            'std_income' => round($stdDev),
            'cv_value' => round($cv, 4),
            'suggested_method' => $suggestedMethod,
            'is_volatile' => $cv >= 0.3,
            'sample_count' => $count,
            'history_data' => $monthlyIncomes,
        ];
    }

    /**
     * Hitung Saran Persentase Awal per Kategori via Exponential Moving Average (EMA)
     */
    public function calculateEmaSuggestions(?int $userId = null, float $alpha = 0.35, int $lookbackMonths = 6): array
    {
        $categories = Category::where('type', 'expense')->get();
        if ($categories->isEmpty()) {
            return [];
        }

        $emaSuggestions = [];

        foreach ($categories as $cat) {
            $ratios = [];
            for ($i = $lookbackMonths; $i >= 1; $i--) {
                $mStart = Carbon::now()->subMonths($i)->startOfMonth();
                $mEnd = Carbon::now()->subMonths($i)->endOfMonth();

                $income = (float) Transaction::where('type', 'income')
                    ->whereBetween('date', [$mStart, $mEnd])
                    ->sum('amount');

                $expense = (float) Transaction::where('type', 'expense')
                    ->where('category_id', $cat->id)
                    ->whereBetween('date', [$mStart, $mEnd])
                    ->sum('amount');

                if ($income > 0) {
                    $ratios[] = ($expense / $income) * 100.0;
                }
            }

            if (empty($ratios)) {
                // Fallback default persentase
                $ema = $cat->is_business ? 15.0 : 10.0;
            } else {
                $ema = $ratios[0];
                for ($k = 1; $k < count($ratios); $k++) {
                    $ema = ($alpha * $ratios[$k]) + ((1 - $alpha) * $ema);
                }
            }

            $emaSuggestions[$cat->id] = max(1.0, round($ema, 1));
        }

        // Normalisasi agar total mendekati 100%
        $totalSum = array_sum($emaSuggestions);
        if ($totalSum > 0) {
            foreach ($emaSuggestions as $catId => $val) {
                $emaSuggestions[$catId] = round(($val / $totalSum) * 100, 1);
            }
        }

        return $emaSuggestions;
    }

    /**
     * Hitung Alokasi Surplus (Waterfall Method) ketika Income Aktual > Floor
     */
    public function calculateSurplusAllocation(float $currentIncome, float $incomeFloor, ?int $budgetProfileId = null, ?int $userId = null): array
    {
        $userId = $userId ?? auth()->id();
        $surplus = max(0.0, $currentIncome - $incomeFloor);
        if ($surplus <= 0) {
            return [
                'surplus_amount' => 0,
                'steps' => [],
                'total_allocated' => 0,
            ];
        }

        // 1. Alokasi Pajak & Cadangan (20% dari surplus)
        $taxReserve = round($surplus * 0.20);
        $remaining = $surplus - $taxReserve;

        // 2. Alokasi ke Saving Goal / Top Priority Purchase Wishlist
        $topWishlist = PurchaseWishlist::when($userId, fn($q) => $q->where('user_id', $userId))
            ->where('status', 'saving')
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->first();

        $wishlistAlloc = 0.0;
        $wishlistName = 'Tabungan Finansial & Wishlist';
        if ($topWishlist && $remaining > 0) {
            $shortage = $topWishlist->target_price - $topWishlist->saved_amount;
            $wishlistAlloc = min($remaining * 0.45, max(0, $shortage));
            $wishlistName = $topWishlist->name;
            $remaining -= $wishlistAlloc;
        } else {
            $wishlistAlloc = round($remaining * 0.40);
            $remaining -= $wishlistAlloc;
        }

        // 3. Alokasi Tier 2 (Essential Buffer) (e.g. 25%)
        $tier2Buffer = round($remaining * 0.50);
        $remaining -= $tier2Buffer;

        // 4. Alokasi Tier 3 (Discretionary Lifestyle) (Sisa surplus)
        $tier3Lifestyle = round($remaining);

        return [
            'surplus_amount' => $surplus,
            'steps' => [
                [
                    'order' => 1,
                    'title' => 'Pajak & Cadangan Usaha',
                    'target' => 'Pajak & Dana Darurat Bisnis (20%)',
                    'amount' => $taxReserve,
                    'percentage' => 20,
                    'badge' => 'Pajak & Reserve',
                    'color' => '#64748B',
                ],
                [
                    'order' => 2,
                    'title' => 'Saving Goal & Priority Wishlist',
                    'target' => $wishlistName,
                    'amount' => $wishlistAlloc,
                    'percentage' => $surplus > 0 ? round(($wishlistAlloc / $surplus) * 100) : 0,
                    'badge' => 'Capital Formation',
                    'color' => '#84CC16',
                ],
                [
                    'order' => 3,
                    'title' => 'Tier 2 Essential Buffer',
                    'target' => 'Penguatan Operasional & Kebutuhan Inti',
                    'amount' => $tier2Buffer,
                    'percentage' => $surplus > 0 ? round(($tier2Buffer / $surplus) * 100) : 0,
                    'badge' => 'Essential Buffer',
                    'color' => '#F59E0B',
                ],
                [
                    'order' => 4,
                    'title' => 'Tier 3 Discretionary (Lifestyle)',
                    'target' => 'Reward Pribadi & Rekreasi Terukur',
                    'amount' => $tier3Lifestyle,
                    'percentage' => $surplus > 0 ? round(($tier3Lifestyle / $surplus) * 100) : 0,
                    'badge' => 'Discretionary',
                    'color' => '#3B82F6',
                ],
            ],
            'total_allocated' => $surplus,
        ];
    }

    /**
     * Validasi Statistik Z-Score untuk memeriksa apakah target persentase realistis
     */
    public function validateZScore(float $targetPercentage, array $historicalPercentages): array
    {
        $count = count($historicalPercentages);
        if ($count < 2) {
            return [
                'z_score' => 0.0,
                'status' => 'valid',
                'badge' => 'Normal',
                'badge_color' => 'slate',
                'message' => 'Data historis belum cukup untuk validasi deviasi Z-score.',
            ];
        }

        $mean = array_sum($historicalPercentages) / $count;
        $varianceSum = 0.0;
        foreach ($historicalPercentages as $val) {
            $varianceSum += pow($val - $mean, 2);
        }
        $stdDev = sqrt($varianceSum / ($count - 1));

        if ($stdDev <= 0.001) {
            return [
                'z_score' => 0.0,
                'status' => 'valid',
                'badge' => 'Normal',
                'badge_color' => 'slate',
                'message' => 'Variansi historis stabil.',
            ];
        }

        $z = ($targetPercentage - $mean) / $stdDev;

        if (abs($z) <= 1.5) {
            return [
                'z_score' => round($z, 2),
                'status' => 'realistic',
                'badge' => 'Aman (Within 1.5σ)',
                'badge_color' => 'emerald',
                'message' => 'Target proporsional sesuai pola historis pengeluaran Anda.',
            ];
        } elseif (abs($z) <= 2.0) {
            return [
                'z_score' => round($z, 2),
                'status' => 'moderate',
                'badge' => 'Peringatan (1.5σ - 2.0σ)',
                'badge_color' => 'amber',
                'message' => 'Target deviasi cukup tinggi dari rata-rata historis.',
            ];
        } else {
            return [
                'z_score' => round($z, 2),
                'status' => 'unrealistic',
                'badge' => 'Ekstrem (> 2.0σ)',
                'badge_color' => 'rose',
                'message' => 'Target secara statistik jauh di luar histori belanja tanpa perubahan gaya hidup besar.',
            ];
        }
    }

    /**
     * Menghasilkan Struktur Data Komprehensif untuk Dashboard Budget Allocation Engine
     */
    public function getBudgetDashboardData(?int $userId = null, ?string $month = null): array
    {
        $userId = $userId ?? auth()->id() ?? User::first()?->id;
        $month = $month ?? Carbon::now()->format('Y-m');
        $monthDate = Carbon::parse($month . '-01');
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();

        // 1. Hitung Income Bulan Ini
        $currentMonthIncome = (float) Transaction::where('type', 'income')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 2. Hitung Income Floor & CV
        $floorData = $this->calculateIncomeFloor($userId, 12);
        $incomeFloor = $floorData['income_floor'];
        $method = $floorData['suggested_method'];

        // 3. Simpan/Update Snapshot untuk audit trail
        IncomeFloorSnapshot::updateOrCreate(
            ['user_id' => $userId, 'month' => $month],
            [
                'income_floor_value' => $incomeFloor,
                'cv_value' => $floorData['cv_value'],
                'method_selected' => $method,
                'avg_income' => $floorData['avg_income'],
                'std_income' => $floorData['std_income'],
            ]
        );

        // 4. Ambil Active Budget Profile
        $activeProfile = BudgetProfile::with(['budgetCategories.category', 'budgetCategories.budgetGroup'])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->where('is_active', true)
            ->first();

        // Fallback jika belum ada profile
        if (!$activeProfile) {
            $this->seedInitialBudgetConfiguration($userId);
            $activeProfile = BudgetProfile::with(['budgetCategories.category', 'budgetCategories.budgetGroup'])
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->where('is_active', true)
                ->first();
        }

        // 5. Hitung Base Baseline Budgeting
        $baseBudgetIncome = ($method === 'floor') ? min($currentMonthIncome, $incomeFloor) : $floorData['avg_income'];
        if ($baseBudgetIncome <= 0) {
            $baseBudgetIncome = $incomeFloor;
        }

        // 6. Hitung Pengeluaran Aktual per Kategori
        $categoryExpenses = Transaction::where('type', 'expense')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->toArray();

        // 7. Hitung Rincian per Group & Tier
        $groups = BudgetGroup::all();
        $groupBreakdown = [];
        $totalTargetPercentage = 0;
        $tier1TotalBudget = 0;

        foreach ($groups as $group) {
            $categoriesInGroup = $activeProfile ? $activeProfile->budgetCategories->where('budget_group_id', $group->id) : collect();

            $groupTargetPct = (float) $categoriesInGroup->sum('target_percentage');
            $totalTargetPercentage += $groupTargetPct;

            $groupBudgetCap = ($groupTargetPct / 100) * $baseBudgetIncome;
            $groupActualSpent = 0;
            $catDetails = [];

            foreach ($categoriesInGroup as $bCat) {
                $actual = (float) ($categoryExpenses[$bCat->category_id] ?? 0);
                $groupActualSpent += $actual;
                $catBudgetCap = ($bCat->target_percentage / 100) * $baseBudgetIncome;

                if ($bCat->priority_tier === 1) {
                    $tier1TotalBudget += $catBudgetCap;
                }

                $catDetails[] = [
                    'budget_category_id' => $bCat->id,
                    'category_id' => $bCat->category_id,
                    'name' => $bCat->category->name ?? 'Kategori',
                    'color' => $bCat->category->color ?? '#64748B',
                    'priority_tier' => $bCat->priority_tier,
                    'priority_label' => $bCat->priority_label,
                    'target_percentage' => $bCat->target_percentage,
                    'budget_cap' => round($catBudgetCap),
                    'actual_spent' => round($actual),
                    'remaining' => round($catBudgetCap - $actual),
                    'progress_percentage' => $catBudgetCap > 0 ? round(($actual / $catBudgetCap) * 100, 1) : 0,
                    'is_overbudget' => $actual > $catBudgetCap && $catBudgetCap > 0,
                ];
            }

            $groupBreakdown[] = [
                'group_id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'icon' => $group->icon,
                'color' => $group->color,
                'target_percentage' => $groupTargetPct,
                'budget_cap' => round($groupBudgetCap),
                'actual_spent' => round($groupActualSpent),
                'progress_percentage' => $groupBudgetCap > 0 ? round(($groupActualSpent / $groupBudgetCap) * 100, 1) : 0,
                'categories' => $catDetails,
            ];
        }

        // 8. Hitung Surplus Waterfall
        $surplusWaterfall = $this->calculateSurplusAllocation($currentMonthIncome, $incomeFloor, $activeProfile?->id, $userId);

        // 9. Warning Validasi Tier 1 vs Income Floor
        $isTier1ExceedingFloor = $tier1TotalBudget > $incomeFloor;

        return [
            'month' => $month,
            'current_income' => $currentMonthIncome,
            'floor_data' => $floorData,
            'income_floor' => $incomeFloor,
            'active_method' => $method,
            'active_profile' => $activeProfile,
            'total_target_percentage' => round($totalTargetPercentage, 1),
            'tier1_total_budget' => $tier1TotalBudget,
            'is_tier1_exceeding_floor' => $isTier1ExceedingFloor,
            'group_breakdown' => $groupBreakdown,
            'surplus_waterfall' => $surplusWaterfall,
            'base_budget_income' => $baseBudgetIncome,
        ];
    }

    /**
     * Daftar 6 Persona Finansial Universal
     */
    public function getAvailablePersonas(): array
    {
        return [
            'creative_media' => [
                'name' => 'Freelance Kreatif & Media',
                'description' => 'Fotografer, videografer, desainer grafis, editor, animator, konten kreator.',
                'icon' => 'camera',
                'badge' => 'Creative & Gear Amortization',
                'method' => 'floor',
                'incomes' => [
                    ['name' => 'Project Foto & Video', 'is_business' => true, 'color' => '#10B981', 'icon' => 'camera'],
                    ['name' => 'Design & Creative Fee', 'is_business' => true, 'color' => '#059669', 'icon' => 'briefcase'],
                    ['name' => 'Royalti / Lisensi Karya', 'is_business' => true, 'color' => '#14B8A6', 'icon' => 'trending-up'],
                ],
                'expenses' => [
                    ['name' => 'Sewa Kamera & Gear', 'group' => 'business_cost', 'tier' => 1, 'pct' => 15.0, 'is_business' => true, 'color' => '#E11D48', 'icon' => 'camera'],
                    ['name' => 'Fee Kru & Asisten', 'group' => 'business_cost', 'tier' => 1, 'pct' => 15.0, 'is_business' => true, 'color' => '#475569', 'icon' => 'users'],
                    ['name' => 'Software Adobe/Figma', 'group' => 'self_dev', 'tier' => 2, 'pct' => 10.0, 'is_business' => true, 'color' => '#8B5CF6', 'icon' => 'laptop'],
                    ['name' => 'Makan & Kebutuhan Pokok', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 20.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Listrik, Wifi & Studio', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 15.0, 'is_business' => false, 'color' => '#EAB308', 'icon' => 'zap'],
                    ['name' => 'Kopi & Lifestyle', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 10.0, 'is_business' => false, 'color' => '#F97316', 'icon' => 'coffee'],
                    ['name' => 'Tabungan Sinking Fund Gear', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 10.0, 'is_business' => true, 'color' => '#059669', 'icon' => 'piggy-bank'],
                    ['name' => 'Cadangan Pajak & Darurat', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 5.0, 'is_business' => true, 'color' => '#0EA5E9', 'icon' => 'shield'],
                ]
            ],
            'it_tech' => [
                'name' => 'Freelance IT, Developer & Digital',
                'description' => 'Software developer, tech consultant, UI/UX, digital ads, DevOps.',
                'icon' => 'laptop',
                'badge' => 'Tech Retainer & Cloud Costs',
                'method' => 'floor',
                'incomes' => [
                    ['name' => 'Project Dev & Coding', 'is_business' => true, 'color' => '#10B981', 'icon' => 'laptop'],
                    ['name' => 'Monthly Retainer Klien', 'is_business' => true, 'color' => '#059669', 'icon' => 'repeat'],
                    ['name' => 'Maintenance / SLA Contract', 'is_business' => true, 'color' => '#14B8A6', 'icon' => 'server'],
                ],
                'expenses' => [
                    ['name' => 'Server Cloud & API (AWS/Vercel)', 'group' => 'business_cost', 'tier' => 1, 'pct' => 15.0, 'is_business' => true, 'color' => '#F97316', 'icon' => 'server'],
                    ['name' => 'Internet High-Speed & Wifi', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 10.0, 'is_business' => false, 'color' => '#EAB308', 'icon' => 'zap'],
                    ['name' => 'Makan & Kebutuhan Pokok', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 25.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Sewa Tempat / Co-working', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 15.0, 'is_business' => false, 'color' => '#E11D48', 'icon' => 'home'],
                    ['name' => 'Tools Dev & Lisensi IDE', 'group' => 'self_dev', 'tier' => 2, 'pct' => 10.0, 'is_business' => true, 'color' => '#8B5CF6', 'icon' => 'book-open'],
                    ['name' => 'Lifestyle & Gaming/Hobi', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 10.0, 'is_business' => false, 'color' => '#6366F1', 'icon' => 'film'],
                    ['name' => 'Tabungan Hardware/Laptop', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 10.0, 'is_business' => true, 'color' => '#059669', 'icon' => 'piggy-bank'],
                    ['name' => 'Cadangan Pajak & Darurat', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 5.0, 'is_business' => true, 'color' => '#0EA5E9', 'icon' => 'shield'],
                ]
            ],
            'consultant_pro' => [
                'name' => 'Jasa Profesional, Konsultan & Penulis',
                'description' => 'Konsultan bisnis, akuntan, translator, copywriter, researcher, trainer.',
                'icon' => 'book-open',
                'badge' => 'Self-Dev & Knowledge Capital',
                'method' => 'floor',
                'incomes' => [
                    ['name' => 'Fee Konsultasi & Advisory', 'is_business' => true, 'color' => '#10B981', 'icon' => 'briefcase'],
                    ['name' => 'Honor Penulisan & Riset', 'is_business' => true, 'color' => '#059669', 'icon' => 'book-open'],
                    ['name' => 'Workshop / Pembicara', 'is_business' => true, 'color' => '#14B8A6', 'icon' => 'mic'],
                ],
                'expenses' => [
                    ['name' => 'Meeting & Networking Klien', 'group' => 'business_cost', 'tier' => 2, 'pct' => 15.0, 'is_business' => true, 'color' => '#475569', 'icon' => 'users'],
                    ['name' => 'Makan & Kebutuhan Rumah', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 30.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Listrik, Wifi & Komunikasi', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 10.0, 'is_business' => false, 'color' => '#EAB308', 'icon' => 'zap'],
                    ['name' => 'Buku, Riset & Sertifikasi', 'group' => 'self_dev', 'tier' => 2, 'pct' => 15.0, 'is_business' => false, 'color' => '#8B5CF6', 'icon' => 'book-open'],
                    ['name' => 'Lifestyle & Rekreasi', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 10.0, 'is_business' => false, 'color' => '#F97316', 'icon' => 'coffee'],
                    ['name' => 'Tabungan & Investasi Portofolio', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 15.0, 'is_business' => false, 'color' => '#059669', 'icon' => 'piggy-bank'],
                    ['name' => 'Cadangan Pajak & Proteksi', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 5.0, 'is_business' => true, 'color' => '#0EA5E9', 'icon' => 'shield'],
                ]
            ],
            'umkm_business' => [
                'name' => 'UMKM, Toko Online & Usaha Mandiri',
                'description' => 'Online shop, retail, merchant e-commerce, kuliner / F&B, jasa mandiri.',
                'icon' => 'shopping-bag',
                'badge' => 'COGS & Ad-Spend Cashflow',
                'method' => 'floor',
                'incomes' => [
                    ['name' => 'Penjualan Produk / Omset', 'is_business' => true, 'color' => '#10B981', 'icon' => 'shopping-bag'],
                    ['name' => 'Penjualan Grosir / Reseller', 'is_business' => true, 'color' => '#059669', 'icon' => 'users'],
                    ['name' => 'Event / Bazar Offline', 'is_business' => true, 'color' => '#14B8A6', 'icon' => 'calendar'],
                ],
                'expenses' => [
                    ['name' => 'HPP & Stok Barang Dagang', 'group' => 'business_cost', 'tier' => 1, 'pct' => 30.0, 'is_business' => true, 'color' => '#E11D48', 'icon' => 'shopping-bag'],
                    ['name' => 'Iklan Meta & TikTok Ads', 'group' => 'business_cost', 'tier' => 1, 'pct' => 15.0, 'is_business' => true, 'color' => '#F97316', 'icon' => 'trending-up'],
                    ['name' => 'Packing & Biaya Ekspedisi', 'group' => 'business_cost', 'tier' => 1, 'pct' => 10.0, 'is_business' => true, 'color' => '#475569', 'icon' => 'truck'],
                    ['name' => 'Sewa Tempat & Utilitas Usaha', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 15.0, 'is_business' => true, 'color' => '#EAB308', 'icon' => 'home'],
                    ['name' => 'Gaji Pribadi (Prive Owner)', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 15.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Lifestyle & Makan Santai', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 5.0, 'is_business' => false, 'color' => '#6366F1', 'icon' => 'coffee'],
                    ['name' => 'Dana Cadangan Kas Operasional', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 10.0, 'is_business' => true, 'color' => '#0EA5E9', 'icon' => 'shield'],
                ]
            ],
            'employee_salary' => [
                'name' => 'Karyawan / Pegawai Gaji Tetap',
                'description' => 'Pegawai swasta, ASN/PNS, BUMN dengan pendapatan bulanan terjadwal (50/30/20).',
                'icon' => 'briefcase',
                'badge' => '50/30/20 Standard Zero-Based',
                'method' => 'average',
                'incomes' => [
                    ['name' => 'Gaji Pokok Bulanan', 'is_business' => false, 'color' => '#10B981', 'icon' => 'briefcase'],
                    ['name' => 'Tunjangan & Lembur', 'is_business' => false, 'color' => '#059669', 'icon' => 'award'],
                    ['name' => 'Bonus / THR', 'is_business' => false, 'color' => '#14B8A6', 'icon' => 'gift'],
                ],
                'expenses' => [
                    ['name' => 'Makan & Belanja Dapur', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 25.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Sewa/Cicilan & Tagihan Listrik', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 20.0, 'is_business' => false, 'color' => '#E11D48', 'icon' => 'home'],
                    ['name' => 'Transportasi Harian (Bensin/KRL)', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 10.0, 'is_business' => false, 'color' => '#EAB308', 'icon' => 'car'],
                    ['name' => 'Tabungan & Investasi Rutin', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 20.0, 'is_business' => false, 'color' => '#059669', 'icon' => 'piggy-bank'],
                    ['name' => 'Lifestyle, Hobi & Hiburan', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 15.0, 'is_business' => false, 'color' => '#6366F1', 'icon' => 'film'],
                    ['name' => 'Kursus & Buku Pengembangan Diri', 'group' => 'self_dev', 'tier' => 2, 'pct' => 5.0, 'is_business' => false, 'color' => '#8B5CF6', 'icon' => 'book-open'],
                    ['name' => 'Dana Darurat Bulanan', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 5.0, 'is_business' => false, 'color' => '#0EA5E9', 'icon' => 'shield'],
                ]
            ],
            'hybrid_sidehustle' => [
                'name' => 'Hybrid (Karyawan + Freelance Sampingan)',
                'description' => 'Pekerja dengan gaji kantor sekaligus menjalankan projek freelance / bisnis sampingan.',
                'icon' => 'sparkles',
                'badge' => 'Dual Stream & Surplus Accelerator',
                'method' => 'floor',
                'incomes' => [
                    ['name' => 'Gaji Pokok Kantor', 'is_business' => false, 'color' => '#10B981', 'icon' => 'briefcase'],
                    ['name' => 'Side-Project Freelance', 'is_business' => true, 'color' => '#059669', 'icon' => 'laptop'],
                    ['name' => 'Komisi / Affiliate Sampingan', 'is_business' => true, 'color' => '#14B8A6', 'icon' => 'trending-up'],
                ],
                'expenses' => [
                    ['name' => 'Kebutuhan Hidup Pokok (Living)', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 35.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Sewa/Cicilan & Listrik/Wifi', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 15.0, 'is_business' => false, 'color' => '#E11D48', 'icon' => 'home'],
                    ['name' => 'Operasional Side-Hustle (Tools)', 'group' => 'business_cost', 'tier' => 2, 'pct' => 10.0, 'is_business' => true, 'color' => '#F97316', 'icon' => 'laptop'],
                    ['name' => 'Tabungan Wishlist Impian', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 15.0, 'is_business' => false, 'color' => '#059669', 'icon' => 'target'],
                    ['name' => 'Investasi Saham / Reksadana', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 10.0, 'is_business' => false, 'color' => '#14B8A6', 'icon' => 'trending-up'],
                    ['name' => 'Lifestyle & Reward Diri', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 10.0, 'is_business' => false, 'color' => '#6366F1', 'icon' => 'film'],
                    ['name' => 'Cadangan Pajak & Darurat', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 5.0, 'is_business' => true, 'color' => '#0EA5E9', 'icon' => 'shield'],
                ]
            ],
            'pelajar_mahasiswa' => [
                'name' => 'Pelajar, Mahasiswa & Pemula',
                'description' => 'Uang saku bulanan, anak kos, fresh graduate, fokus hemat anti-boncos & tabungan awal.',
                'icon' => 'book-open',
                'badge' => 'Smart Anti-Boncos & Hemat',
                'method' => 'average',
                'incomes' => [
                    ['name' => 'Uang Saku & Kiriman', 'is_business' => false, 'color' => '#10B981', 'icon' => 'credit-card'],
                    ['name' => 'Gaji Part-Time / Magang', 'is_business' => false, 'color' => '#059669', 'icon' => 'briefcase'],
                    ['name' => 'Hadiah / Jasa Teman', 'is_business' => false, 'color' => '#14B8A6', 'icon' => 'gift'],
                ],
                'expenses' => [
                    ['name' => 'Sewa Kos & Listrik', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 35.0, 'is_business' => false, 'color' => '#E11D48', 'icon' => 'home'],
                    ['name' => 'Makan & Belanja Harian', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 30.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Bensin / Ongkos Transport', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 10.0, 'is_business' => false, 'color' => '#EAB308', 'icon' => 'car'],
                    ['name' => 'Tugas, Print & Kuota Internet', 'group' => 'self_dev', 'tier' => 2, 'pct' => 10.0, 'is_business' => false, 'color' => '#8B5CF6', 'icon' => 'book-open'],
                    ['name' => 'Nongkrong & Jajan Santai', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 10.0, 'is_business' => false, 'color' => '#6366F1', 'icon' => 'coffee'],
                    ['name' => 'Tabungan Jaga-Jaga', 'group' => 'financial_saving', 'tier' => 2, 'pct' => 5.0, 'is_business' => false, 'color' => '#059669', 'icon' => 'piggy-bank'],
                ]
            ],
            'keluarga_rumahtangga' => [
                'name' => 'Pengelola Rumah Tangga & Keluarga',
                'description' => 'Mengatur kas keluarga, belanja dapur, SPP anak, utilitas rumah, & dana darurat bersama.',
                'icon' => 'home',
                'badge' => 'Family Cashflow & Protection',
                'method' => 'average',
                'incomes' => [
                    ['name' => 'Gaji / Nafkah Bulanan', 'is_business' => false, 'color' => '#10B981', 'icon' => 'briefcase'],
                    ['name' => 'Usaha Sampingan Keluarga', 'is_business' => true, 'color' => '#059669', 'icon' => 'shopping-bag'],
                    ['name' => 'Hasil Investasi / Pasif', 'is_business' => false, 'color' => '#14B8A6', 'icon' => 'trending-up'],
                ],
                'expenses' => [
                    ['name' => 'Belanja Dapur & Makan Keluarga', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 30.0, 'is_business' => false, 'color' => '#10B981', 'icon' => 'coffee'],
                    ['name' => 'Cicilan/Sewa Rumah & Listrik/Air', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 25.0, 'is_business' => false, 'color' => '#E11D48', 'icon' => 'home'],
                    ['name' => 'Biaya Sekolah & Pendidikan Anak', 'group' => 'self_dev', 'tier' => 1, 'pct' => 15.0, 'is_business' => false, 'color' => '#8B5CF6', 'icon' => 'book-open'],
                    ['name' => 'Transportasi & Bensin Keluarga', 'group' => 'fixed_needs', 'tier' => 1, 'pct' => 10.0, 'is_business' => false, 'color' => '#EAB308', 'icon' => 'car'],
                    ['name' => 'Dana Darurat & Asuransi', 'group' => 'tax_reserve', 'tier' => 1, 'pct' => 10.0, 'is_business' => false, 'color' => '#0EA5E9', 'icon' => 'shield'],
                    ['name' => 'Rekreasi & Jalan-Jalan Keluarga', 'group' => 'lifestyle', 'tier' => 3, 'pct' => 10.0, 'is_business' => false, 'color' => '#6366F1', 'icon' => 'film'],
                ]
            ],
        ];
    }

    /**
     * Terapkan Preset Persona Finansial ke Akun Pengguna
     */
    public function applyPersonaPreset(int $userId, string $personaKey, ?string $stability = null, ?string $priority = null): array
    {
        $personas = $this->getAvailablePersonas();
        
        // Alias fallback mapping
        $aliases = [
            'karyawan' => 'employee_salary',
            'karyawan_tetap' => 'employee_salary',
            'umkm' => 'umkm_business',
            'umkm_bisnis' => 'umkm_business',
            'freelance' => 'creative_media',
            'freelance_kreatif' => 'creative_media',
            'pelajar' => 'pelajar_mahasiswa',
            'keluarga' => 'keluarga_rumahtangga',
        ];
        $resolvedKey = $aliases[$personaKey] ?? $personaKey;
        $selected = $personas[$resolvedKey] ?? $personas['creative_media'];

        // 1. Pastikan Budget Groups ada
        $this->ensureBudgetGroupsExist();

        // 2. Buat / Update Budget Profile
        $profileName = $selected['name'];
        $method = $stability === 'stable' ? 'average' : $selected['method'];

        $profile = BudgetProfile::updateOrCreate(
            ['user_id' => $userId, 'name' => $profileName],
            ['is_active' => true, 'method' => $method]
        );

        // Deactive other profiles
        BudgetProfile::where('user_id', $userId)
            ->where('id', '!=', $profile->id)
            ->update(['is_active' => false]);

        // 3. Buat Kategori Income
        foreach ($selected['incomes'] as $inc) {
            Category::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name' => $inc['name'],
                    'type' => 'income',
                ],
                [
                    'is_business' => $inc['is_business'],
                    'color' => $inc['color'],
                    'icon' => $inc['icon'],
                ]
            );
        }

        // 4. Buat Kategori Expense & Mapping BudgetCategory
        $createdExpenses = [];
        foreach ($selected['expenses'] as $exp) {
            $cat = Category::firstOrCreate(
                [
                    'user_id' => $userId,
                    'name' => $exp['name'],
                    'type' => 'expense',
                ],
                [
                    'is_business' => $exp['is_business'],
                    'color' => $exp['color'],
                    'icon' => $exp['icon'],
                ]
            );

            $group = BudgetGroup::where('slug', $exp['group'])->first();
            if ($group) {
                // Adjust percentage slightly if user selected a priority
                $pct = $exp['pct'];
                if ($priority === 'emergency' && $exp['group'] === 'tax_reserve') {
                    $pct += 5.0;
                } elseif ($priority === 'wishlist' && $exp['group'] === 'financial_saving') {
                    $pct += 5.0;
                }

                BudgetCategory::updateOrCreate(
                    [
                        'budget_profile_id' => $profile->id,
                        'category_id' => $cat->id,
                    ],
                    [
                        'budget_group_id' => $group->id,
                        'priority_tier' => $exp['tier'],
                        'target_percentage' => $pct,
                    ]
                );
            }

            $createdExpenses[] = $cat;
        }

        return [
            'profile' => $profile,
            'persona' => $selected,
            'expense_count' => count($createdExpenses),
        ];
    }

    /**
     * Memastikan Master Budget Groups Ada di Database
     */
    public function ensureBudgetGroupsExist(): void
    {
        $groupsData = [
            [
                'name' => 'Kebutuhan Wajib (Fixed Needs)',
                'slug' => 'fixed_needs',
                'default_priority_tier' => 1,
                'icon' => 'home',
                'color' => '#E11D48',
                'description' => 'Biaya pokok yang harus dibayar berapa pun incomenya (sewa, cicilan, listrik).',
            ],
            [
                'name' => 'Operasional Kerja (Business Cost)',
                'slug' => 'business_cost',
                'default_priority_tier' => 1,
                'icon' => 'briefcase',
                'color' => '#475569',
                'description' => 'Pengeluaran alat, transport, server, dan fee tim untuk menghasilkan uang.',
            ],
            [
                'name' => 'Lifestyle (Wants)',
                'slug' => 'lifestyle',
                'default_priority_tier' => 3,
                'icon' => 'shopping-bag',
                'color' => '#84CC16',
                'description' => 'Hiburan, makan santai, belanja hobi, dan jalan-jalan.',
            ],
            [
                'name' => 'Pengembangan Diri',
                'slug' => 'self_dev',
                'default_priority_tier' => 2,
                'icon' => 'book-open',
                'color' => '#8B5CF6',
                'description' => 'Kursus, buku, lisensi software keahlian, dan workshop.',
            ],
            [
                'name' => 'Finansial (Saving/Investasi)',
                'slug' => 'financial_saving',
                'default_priority_tier' => 2,
                'icon' => 'piggy-bank',
                'color' => '#059669',
                'description' => 'Pembentukan modal, dana darurat, dan investasi masa depan.',
            ],
            [
                'name' => 'Pajak & Cadangan',
                'slug' => 'tax_reserve',
                'default_priority_tier' => 1,
                'icon' => 'shield-check',
                'color' => '#0EA5E9',
                'description' => 'Pos simpanan pajak penghasilan dan bantalan risiko usaha.',
            ],
        ];

        foreach ($groupsData as $gd) {
            BudgetGroup::firstOrCreate(['slug' => $gd['slug']], $gd);
        }
    }

    /**
     * Inisialisasi awal Master Budget Groups & default profile
     */
    public function seedInitialBudgetConfiguration(?int $userId = null): void
    {
        $userId = $userId ?? auth()->id() ?? User::first()?->id;
        if (!$userId) return;

        $this->ensureBudgetGroupsExist();
        $this->applyPersonaPreset($userId, 'creative_media');
    }
}

