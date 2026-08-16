<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Client;
use App\Models\FinancialGoal;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\PurchaseSaving;
use App\Models\PurchaseWishlist;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WishlistPriceHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default User (Zaki)
        $user = User::create([
            'name' => 'Zaki Pratama',
            'email' => 'zaki@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Accounts (Multi-Account: Bank, E-Wallet, Cash with real brand styling)
        $bca = Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'account_number' => '8210984123',
            'current_balance' => 14500000,
            'initial_balance' => 14500000,
            'color' => '#003B70',
            'icon' => 'building-2',
            'is_active' => true,
        ]);

        $mandiri = Account::create([
            'user_id' => $user->id,
            'name' => 'Bank Mandiri',
            'type' => 'bank',
            'account_number' => '137001928341',
            'current_balance' => 8200000,
            'initial_balance' => 8200000,
            'color' => '#002D62',
            'icon' => 'landmark',
            'is_active' => true,
        ]);

        $gopay = Account::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'account_number' => '081234567890',
            'current_balance' => 750000,
            'initial_balance' => 750000,
            'color' => '#00AA13',
            'icon' => 'smartphone',
            'is_active' => true,
        ]);

        $cash = Account::create([
            'user_id' => $user->id,
            'name' => 'Dompet Tunai',
            'type' => 'cash',
            'current_balance' => 450000,
            'initial_balance' => 450000,
            'color' => '#F59E0B',
            'icon' => 'banknote',
            'is_active' => true,
        ]);

        // 3. Create Categories (Income & Expense, Personal vs Business)
        $catProjectIncome = Category::create(['user_id' => $user->id, 'name' => 'Project Freelance', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#10B981', 'is_business' => true]);
        $catRetainerIncome = Category::create(['user_id' => $user->id, 'name' => 'Monthly Retainer', 'type' => 'income', 'icon' => 'repeat', 'color' => '#059669', 'is_business' => true]);
        $catPassiveIncome = Category::create(['user_id' => $user->id, 'name' => 'Passive / Asset', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#14B8A6', 'is_business' => false]);

        // Expenses
        $catEquipment = Category::create(['user_id' => $user->id, 'name' => 'Equipment & Tools', 'type' => 'expense', 'icon' => 'camera', 'color' => '#EF4444', 'is_business' => true]);
        $catServer = Category::create(['user_id' => $user->id, 'name' => 'Software & Server Hosting', 'type' => 'expense', 'icon' => 'server', 'color' => '#F97316', 'is_business' => true]);
        $catTransport = Category::create(['user_id' => $user->id, 'name' => 'Transport & Operasional', 'type' => 'expense', 'icon' => 'car', 'color' => '#EAB308', 'is_business' => true]);
        $catFood = Category::create(['user_id' => $user->id, 'name' => 'Makan & Minum', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#8B5CF6', 'is_business' => false]);
        $catBills = Category::create(['user_id' => $user->id, 'name' => 'Listrik, Wifi & Kos', 'type' => 'expense', 'icon' => 'zap', 'color' => '#EC4899', 'is_business' => false]);
        $catLifestyle = Category::create(['user_id' => $user->id, 'name' => 'Lifestyle & Hiburan', 'type' => 'expense', 'icon' => 'film', 'color' => '#6366F1', 'is_business' => false]);

        // 4. Budgets (Percentage-based)
        Budget::create(['user_id' => $user->id, 'category_id' => $catFood->id, 'percentage' => 20.00, 'period_month' => now()->month, 'period_year' => now()->year]);
        Budget::create(['user_id' => $user->id, 'category_id' => $catBills->id, 'percentage' => 15.00, 'period_month' => now()->month, 'period_year' => now()->year]);
        Budget::create(['user_id' => $user->id, 'category_id' => $catServer->id, 'percentage' => 10.00, 'period_month' => now()->month, 'period_year' => now()->year]);
        Budget::create(['user_id' => $user->id, 'category_id' => $catLifestyle->id, 'percentage' => 10.00, 'period_month' => now()->month, 'period_year' => now()->year]);

        // 5. Clients & Projects
        $clientA = Client::create([
            'user_id' => $user->id,
            'name' => 'PT Media Kreasi Nusantara',
            'company' => 'Media Kreasi Group',
            'email' => 'finance@mediakreasi.id',
            'phone' => '081122334455',
            'status' => 'active',
        ]);

        $clientB = Client::create([
            'user_id' => $user->id,
            'name' => 'Panitia Muswil Pemuda',
            'company' => 'PW Pemuda Jawa Tengah',
            'email' => 'muswil@pemuda.org',
            'phone' => '085712349988',
            'status' => 'active',
        ]);

        $clientC = Client::create([
            'user_id' => $user->id,
            'name' => 'Klinik Sakina Medika',
            'company' => 'Sakina Group',
            'email' => 'dr.sakina@gmail.com',
            'phone' => '081399887766',
            'status' => 'active',
        ]);

        // Project 1: Livestreaming Muswil 3 Hari
        $proj1 = Project::create([
            'user_id' => $user->id,
            'client_id' => $clientB->id,
            'name' => 'Multi-Cam Livestreaming Muswil 3 Hari',
            'category' => 'livestream',
            'description' => 'Setup 4 camera broadcast, vMix switcher, wireless video transmission',
            'total_revenue' => 12500000,
            'start_date' => now()->subDays(10),
            'deadline' => now()->addDays(5),
            'status' => 'in_progress',
        ]);

        ProjectCost::create([
            'project_id' => $proj1->id,
            'category_id' => $catEquipment->id,
            'description' => 'Sewa Wireless Video Hollyland 400S Pro & SDI Cable',
            'amount' => 1200000,
            'date' => now()->subDays(8),
        ]);

        ProjectCost::create([
            'project_id' => $proj1->id,
            'category_id' => $catTransport->id,
            'description' => 'Konsumsi & Operasional Tim 4 Orang',
            'amount' => 850000,
            'date' => now()->subDays(7),
        ]);

        Invoice::create([
            'project_id' => $proj1->id,
            'invoice_number' => 'INV-2026-08-001',
            'amount' => 6250000, // DP 50%
            'issue_date' => now()->subDays(10),
            'due_date' => now()->subDays(5),
            'status' => 'paid',
            'paid_at' => now()->subDays(6),
            'paid_to_account_id' => $bca->id,
        ]);

        Invoice::create([
            'project_id' => $proj1->id,
            'invoice_number' => 'INV-2026-08-002',
            'amount' => 6250000, // Pelunasan
            'issue_date' => now()->subDays(1),
            'due_date' => now()->addDays(7),
            'status' => 'sent',
        ]);

        // Project 2: Web App Sistem Reservasi & Kasir
        $proj2 = Project::create([
            'user_id' => $user->id,
            'client_id' => $clientC->id,
            'name' => 'Pengembangan Sistem Antrian & Reservasi Web',
            'category' => 'web_dev',
            'description' => 'Fullstack web app Laravel + Vue, integrasi WhatsApp Gateway',
            'total_revenue' => 15000000,
            'start_date' => now()->subDays(25),
            'deadline' => now()->subDays(2),
            'completed_date' => now()->subDays(2),
            'status' => 'completed',
        ]);

        ProjectCost::create([
            'project_id' => $proj2->id,
            'category_id' => $catServer->id,
            'description' => 'VPS Server DigitalOcean 1 Tahun & Domain',
            'amount' => 1400000,
            'date' => now()->subDays(20),
        ]);

        Invoice::create([
            'project_id' => $proj2->id,
            'invoice_number' => 'INV-2026-07-009',
            'amount' => 15000000,
            'issue_date' => now()->subDays(5),
            'due_date' => now()->addDays(10),
            'status' => 'paid',
            'paid_at' => now()->subDays(2),
            'paid_to_account_id' => $mandiri->id,
        ]);

        // 6. Purchase Wishlists (PRD v1.1 Data Model)
        $wishlist1 = PurchaseWishlist::create([
            'user_id' => $user->id,
            'name' => 'DJI Pocket 4 Creator Combo',
            'category' => 'Alat Kerja',
            'target_price' => 8500000,
            'current_price' => 8000000,
            'product_url' => 'https://tokopedia.com/sample-dji-pocket',
            'priority' => 'high',
            'target_date' => now()->addMonths(4)->endOfMonth(),
            'saved_amount' => 0,
            'status' => 'planning',
            'notes' => 'Untuk upgrade kualitas vlogging, b-roll konten livestreaming dan backstage.',
        ]);

        WishlistPriceHistory::create([
            'wishlist_id' => $wishlist1->id,
            'price' => 8500000,
            'recorded_at' => now()->subDays(20),
            'notes' => 'Harga awal rilis resmi',
        ]);

        WishlistPriceHistory::create([
            'wishlist_id' => $wishlist1->id,
            'price' => 8000000,
            'recorded_at' => now()->subDays(3),
            'notes' => 'Promo diskon merchant',
        ]);

        // Alokasi tabungan ke DJI Pocket 4
        PurchaseSaving::create([
            'wishlist_id' => $wishlist1->id,
            'account_id' => $bca->id,
            'amount' => 3000000,
            'date' => now()->subDays(15),
            'note' => 'Alokasi dari DP Project Muswil',
        ]);

        PurchaseSaving::create([
            'wishlist_id' => $wishlist1->id,
            'account_id' => $mandiri->id,
            'amount' => 2000000,
            'date' => now()->subDays(2),
            'note' => 'Alokasi dari Pelunasan Web Sakina',
        ]);

        // Wishlist 2: NVMe SSD 2TB
        $wishlist2 = PurchaseWishlist::create([
            'user_id' => $user->id,
            'name' => 'Samsung 990 Pro SSD 2TB M.2',
            'category' => 'Alat Kerja',
            'target_price' => 2700000,
            'current_price' => 2500000,
            'product_url' => 'https://tokopedia.com/samsung-990-pro-2tb',
            'priority' => 'medium',
            'target_date' => now()->addMonths(2)->endOfMonth(),
            'saved_amount' => 0,
            'status' => 'planning',
            'notes' => 'Penyimpanan footage 4K 60fps ProRes.',
        ]);

        PurchaseSaving::create([
            'wishlist_id' => $wishlist2->id,
            'account_id' => $bca->id,
            'amount' => 1500000,
            'date' => now()->subDays(5),
            'note' => 'Tabungan rutin bulanan',
        ]);

        // Wishlist 3: Monitor 27 4K
        $wishlist3 = PurchaseWishlist::create([
            'user_id' => $user->id,
            'name' => 'Dell UltraSharp 27 4K Color Accurate',
            'category' => 'Gadget',
            'target_price' => 4500000,
            'current_price' => 4000000,
            'product_url' => 'https://tokopedia.com/dell-ultrasharp-27',
            'priority' => 'low',
            'target_date' => now()->addMonths(6)->endOfMonth(),
            'saved_amount' => 0,
            'status' => 'planning',
            'notes' => 'Color grading video & coding workspace dual-monitor.',
        ]);

        PurchaseSaving::create([
            'wishlist_id' => $wishlist3->id,
            'account_id' => $bca->id,
            'amount' => 500000,
            'date' => now()->subDays(1),
            'note' => 'Setoran awal',
        ]);

        // 7. General Financial Goal
        FinancialGoal::create([
            'user_id' => $user->id,
            'title' => 'Dana Darurat 6 Bulan Pengeluaran',
            'target_amount' => 30000000,
            'current_amount' => 18000000,
            'target_date' => now()->addYear(),
            'category' => 'emergency_fund',
            'status' => 'in_progress',
        ]);

        // 8. 6-Month Realistic Historical Transactions
        Transaction::withoutEvents(function () use ($user, $bca, $mandiri, $gopay, $catProjectIncome, $catRetainerIncome, $catFood, $catBills, $catTransport, $catLifestyle, $catServer, $catEquipment) {
            
            // Month -5 (March 2026)
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 14000000, 'date' => now()->subMonths(5)->setDay(10), 'description' => 'Project Video Profile Perusahaan']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catBills->id, 'type' => 'expense', 'amount' => 2500000, 'date' => now()->subMonths(5)->setDay(15), 'description' => 'Listrik & Sewa Tempat']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $gopay->id, 'category_id' => $catFood->id, 'type' => 'expense', 'amount' => 1800000, 'date' => now()->subMonths(5)->setDay(20), 'description' => 'Makan Bulanan']);

            // Month -4 (April 2026)
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 18500000, 'date' => now()->subMonths(4)->setDay(8), 'description' => 'Project E-Commerce Redesign']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $mandiri->id, 'category_id' => $catEquipment->id, 'type' => 'expense', 'amount' => 3200000, 'date' => now()->subMonths(4)->setDay(12), 'description' => 'Beli Mic Wireless Rode']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $gopay->id, 'category_id' => $catFood->id, 'type' => 'expense', 'amount' => 2100000, 'date' => now()->subMonths(4)->setDay(25), 'description' => 'Makan & Konsumsi Tim']);

            // Month -3 (May 2026)
            Transaction::create(['user_id' => $user->id, 'account_id' => $mandiri->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 12000000, 'date' => now()->subMonths(3)->setDay(14), 'description' => 'Dokumentasi Wisuda 2 Hari']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catBills->id, 'type' => 'expense', 'amount' => 2400000, 'date' => now()->subMonths(3)->setDay(16), 'description' => 'Internet Fiber & Listrik']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $gopay->id, 'category_id' => $catLifestyle->id, 'type' => 'expense', 'amount' => 1200000, 'date' => now()->subMonths(3)->setDay(28), 'description' => 'Liburan & Healing']);

            // Month -2 (June 2026)
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 22000000, 'date' => now()->subMonths(2)->setDay(5), 'description' => 'Sistem Informasi Akademik Web']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catServer->id, 'type' => 'expense', 'amount' => 1800000, 'date' => now()->subMonths(2)->setDay(10), 'description' => 'Server Cloud AWS & DigitalOcean']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $gopay->id, 'category_id' => $catFood->id, 'type' => 'expense', 'amount' => 2300000, 'date' => now()->subMonths(2)->setDay(22), 'description' => 'Konsumsi & Kuliner']);

            // Month -1 (July 2026)
            Transaction::create(['user_id' => $user->id, 'account_id' => $mandiri->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 16500000, 'date' => now()->subMonths(1)->setDay(18), 'description' => 'Livestreaming Webinar Nasional']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catTransport->id, 'type' => 'expense', 'amount' => 1100000, 'date' => now()->subMonths(1)->setDay(20), 'description' => 'Transport Luar Kota & Sewa Mobil']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catBills->id, 'type' => 'expense', 'amount' => 2500000, 'date' => now()->subMonths(1)->setDay(25), 'description' => 'Sewa Studio & Listrik']);

            // Current Month (August 2026)
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 6250000, 'date' => now()->subDays(6), 'description' => 'Pembayaran DP Muswil 50%']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $mandiri->id, 'category_id' => $catProjectIncome->id, 'type' => 'income', 'amount' => 15000000, 'date' => now()->subDays(2), 'description' => 'Pelunasan Web Antrian Sakina Medika']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catBills->id, 'type' => 'expense', 'amount' => 1250000, 'date' => now()->subDays(12), 'description' => 'Pembayaran Sewa Studio & Listrik']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $gopay->id, 'category_id' => $catFood->id, 'type' => 'expense', 'amount' => 145000, 'date' => now()->subDays(4), 'description' => 'Makan Malam Tim Editing']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $bca->id, 'category_id' => $catTransport->id, 'type' => 'expense', 'amount' => 300000, 'date' => now()->subDays(3), 'description' => 'Bensin & Tol Survey Lokasi Video']);
            Transaction::create(['user_id' => $user->id, 'account_id' => $gopay->id, 'category_id' => $catLifestyle->id, 'type' => 'expense', 'amount' => 180000, 'date' => now()->subDays(1), 'description' => 'Langganan Spotify & Netflix']);
        });

        // 9. Initialize PRD Addendum v1.2 Budget Engine Configuration
        app(\App\Services\BudgetAllocationService::class)->seedInitialBudgetConfiguration($user->id);
    }
}
