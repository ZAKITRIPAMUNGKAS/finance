<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'onboarding_completed',
        'role',
        'subscription_tier',
        'trial_ends_at',
        'subscription_ends_at',
        'is_banned',
        'banned_reason',
        'last_login_at',
        'monthly_ai_usage',
        'ai_usage_reset_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'last_login_at' => 'datetime',
            'ai_usage_reset_at' => 'datetime',
            'monthly_ai_usage' => 'integer',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'is_banned' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (strtolower(trim($user->email ?? '')) === 'zakitripamungkas03@gmail.com') {
                $user->role = 'admin';
                $user->subscription_tier = 'lifetime';
                $user->trial_ends_at = null;
                $user->subscription_ends_at = null;
            } else {
                if (empty($user->subscription_tier)) {
                    $user->subscription_tier = 'trial';
                }
                if (empty($user->trial_ends_at) && $user->subscription_tier === 'trial') {
                    $user->trial_ends_at = now()->addDays(14);
                }
                if (empty($user->role)) {
                    $user->role = 'user';
                }
            }
        });
    }

    public function isAdmin(): bool
    {
        if (strtolower(trim($this->email ?? '')) === 'zakitripamungkas03@gmail.com') {
            return true;
        }
        return $this->role === 'admin';
    }

    public function isLifetime(): bool
    {
        return $this->subscription_tier === 'lifetime';
    }

    public function isTrial(): bool
    {
        return $this->subscription_tier === 'trial' && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isPro(): bool
    {
        if ($this->isAdmin() || $this->isLifetime()) {
            return true;
        }
        if ($this->subscription_tier === 'pro' && ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture())) {
            return true;
        }
        return $this->isTrial();
    }

    public function isFree(): bool
    {
        return !$this->isPro() && !$this->isLifetime() && !$this->isTrial();
    }

    public function canCreateAccount(): bool
    {
        if ($this->isPro()) {
            return true;
        }
        return $this->accounts()->count() < 2;
    }

    public function canCreateProject(): bool
    {
        if ($this->isPro()) {
            return true;
        }
        return $this->projects()->count() < 2;
    }

    public function getCurrentMonthlyAiUsage(): int
    {
        if (!$this->ai_usage_reset_at || $this->ai_usage_reset_at->format('Y-m') !== now()->format('Y-m')) {
            return 0;
        }
        return (int) ($this->monthly_ai_usage ?? 0);
    }

    public function canUseAiVoiceOrScan(): bool
    {
        if ($this->isPro()) {
            return true;
        }
        return $this->getCurrentMonthlyAiUsage() < 5;
    }

    public function incrementAiUsage(): void
    {
        if (!$this->ai_usage_reset_at || $this->ai_usage_reset_at->format('Y-m') !== now()->format('Y-m')) {
            $this->monthly_ai_usage = 1;
            $this->ai_usage_reset_at = now();
        } else {
            $this->monthly_ai_usage = ($this->monthly_ai_usage ?? 0) + 1;
        }
        $this->saveQuietly();
    }

    public function getRemainingAiScansAttribute(): int
    {
        if ($this->isPro()) {
            return 999999;
        }
        return max(0, 5 - $this->getCurrentMonthlyAiUsage());
    }

    public function getRemainingTrialDaysAttribute(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at->isPast()) {
            return 0;
        }
        return (int) now()->diffInDays($this->trial_ends_at, false) + 1;
    }

    public function getTierLabelAttribute(): string
    {
        if ($this->isAdmin()) return 'Superadmin';
        if ($this->isLifetime()) return 'Lifetime VIP';
        if ($this->subscription_tier === 'pro' && ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture())) return 'Pro Member';
        if ($this->isTrial()) return 'Free Trial (' . $this->remaining_trial_days . ' hari)';
        return 'Free Starter';
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function purchaseWishlists(): HasMany
    {
        return $this->hasMany(PurchaseWishlist::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(PurchaseWishlist::class);
    }

    public function financialGoals(): HasMany
    {
        return $this->hasMany(FinancialGoal::class);
    }
}
