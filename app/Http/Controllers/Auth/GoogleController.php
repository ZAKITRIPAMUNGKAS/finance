<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Services\BudgetAllocationService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google OAuth authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google OAuth.
     */
    public function callback(BudgetAllocationService $budgetService)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal menghubungkan dengan Google. Silakan coba lagi atau masuk dengan email.'
            ]);
        }

        if (empty($googleUser->getEmail())) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google Anda tidak menyediakan alamat email yang valid.'
            ]);
        }

        // Check if user already exists with this email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Link google_id if not linked yet, and ensure email is verified
            $user->google_id = $googleUser->getId();
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
        } else {
            // Create brand new verified user
            $user = User::create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Pengguna Google'),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'onboarding_completed' => false,
            ]);

            // Initialize Clean Starter Accounts (Rp 0 Balance)
            Account::create([
                'user_id' => $user->id,
                'name' => 'BCA Utama',
                'type' => 'bank',
                'initial_balance' => 0,
                'current_balance' => 0,
                'color' => '#003B70',
                'icon' => 'building-2',
                'is_active' => true,
            ]);

            Account::create([
                'user_id' => $user->id,
                'name' => 'GoPay',
                'type' => 'ewallet',
                'initial_balance' => 0,
                'current_balance' => 0,
                'color' => '#00AA13',
                'icon' => 'smartphone',
                'is_active' => true,
            ]);

            Account::create([
                'user_id' => $user->id,
                'name' => 'Dompet Tunai',
                'type' => 'cash',
                'initial_balance' => 0,
                'current_balance' => 0,
                'color' => '#F59E0B',
                'icon' => 'banknote',
                'is_active' => true,
            ]);

            // Initialize Budget Allocation Profile
            $budgetService->seedInitialBudgetConfiguration($user->id);
        }

        Auth::login($user, true);
        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
