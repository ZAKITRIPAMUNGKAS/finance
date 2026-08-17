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
        try {
            if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Google OAuth belum dikonfigurasi di file .env server. Pastikan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET sudah diisi di .env server.'
                ]);
            }

            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal membuka halaman login Google: ' . $e->getMessage()
            ]);
        }
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

            // Initialize Budget Allocation Profile
            $budgetService->seedInitialBudgetConfiguration($user->id);
        }

        Auth::login($user, true);
        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
