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
     * Inisialisasi Konfigurasi Budget Standar untuk Akun Baru / Seeder
     */
    public function seedInitialBudgetConfiguration(?int $userId = null): void
    {
        $userId = $userId ?? User::first()?->id;

        // 1. Buat 6 Fixed Budget Groups
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

        // Pastikan ada Category default jika belum ada sama sekali
        if (Category::where('type', 'expense')->count() === 0) {
            Category::create(['name' => 'Sewa Kantor & Studio', 'type' => 'expense', 'icon' => 'home', 'color' => '#E11D48', 'is_business' => true]);
            Category::create(['name' => 'Listrik & Internet', 'type' => 'expense', 'icon' => 'zap', 'color' => '#EAB308', 'is_business' => false]);
            Category::create(['name' => 'Sewa Alat & Kru Freelance', 'type' => 'expense', 'icon' => 'camera', 'color' => '#475569', 'is_business' => true]);
            Category::create(['name' => 'Makan & Kebutuhan Pokok', 'type' => 'expense', 'icon' => 'coffee', 'color' => '#10B981', 'is_business' => false]);
            Category::create(['name' => 'Kopi & Nongkrong', 'type' => 'expense', 'icon' => 'coffee', 'color' => '#F97316', 'is_business' => false]);
            Category::create(['name' => 'Langganan Software (Adobe/Figma)', 'type' => 'expense', 'icon' => 'laptop', 'color' => '#8B5CF6', 'is_business' => true]);
            Category::create(['name' => 'Cadangan Pajak & Darurat', 'type' => 'expense', 'icon' => 'shield', 'color' => '#0EA5E9', 'is_business' => true]);
            Category::create(['name' => 'Tabungan Pembelian Alat', 'type' => 'expense', 'icon' => 'piggy-bank', 'color' => '#059669', 'is_business' => true]);
        }

        // 2. Buat Budget Profile Default
        $profile = BudgetProfile::firstOrCreate(
            ['user_id' => $userId, 'name' => 'Bulan Normal (Freelance Adaptive)'],
            ['is_active' => true, 'method' => 'floor']
        );

        // 3. Mapping Kategori Eksisting
        $categories = Category::where('type', 'expense')->get();
        $gFixed = BudgetGroup::where('slug', 'fixed_needs')->first();
        $gBiz = BudgetGroup::where('slug', 'business_cost')->first();
        $gLife = BudgetGroup::where('slug', 'lifestyle')->first();
        $gDev = BudgetGroup::where('slug', 'self_dev')->first();
        $gFin = BudgetGroup::where('slug', 'financial_saving')->first();
        $gTax = BudgetGroup::where('slug', 'tax_reserve')->first();

        // Template persentase awal
        $mappings = [
            'Sewa Kantor & Studio' => ['group' => $gFixed, 'tier' => 1, 'pct' => 15.0],
            'Listrik & Internet' => ['group' => $gFixed, 'tier' => 1, 'pct' => 10.0],
            'Sewa Alat & Kru Freelance' => ['group' => $gBiz, 'tier' => 1, 'pct' => 20.0],
            'Makan & Kebutuhan Pokok' => ['group' => $gFixed, 'tier' => 2, 'pct' => 15.0],
            'Kopi & Nongkrong' => ['group' => $gLife, 'tier' => 3, 'pct' => 10.0],
            'Langganan Software (Adobe/Figma)' => ['group' => $gDev, 'tier' => 2, 'pct' => 10.0],
            'Cadangan Pajak & Darurat' => ['group' => $gTax, 'tier' => 1, 'pct' => 10.0],
            'Tabungan Pembelian Alat' => ['group' => $gFin, 'tier' => 2, 'pct' => 10.0],
        ];

        foreach ($categories as $cat) {
            $map = $mappings[$cat->name] ?? null;
            if (!$map) {
                if ($cat->is_business) {
                    $map = ['group' => $gBiz, 'tier' => 2, 'pct' => 10.0];
                } else {
                    $map = ['group' => $gLife, 'tier' => 3, 'pct' => 5.0];
                }
            }

            BudgetCategory::updateOrCreate(
                [
                    'budget_profile_id' => $profile->id,
                    'category_id' => $cat->id,
                ],
                [
                    'budget_group_id' => $map['group']->id,
                    'priority_tier' => $map['tier'],
                    'target_percentage' => $map['pct'],
                ]
            );
        }
    }
}
