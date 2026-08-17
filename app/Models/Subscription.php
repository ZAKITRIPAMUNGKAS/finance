<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'billing_cycle',
        'billing_date',
        'category_id',
        'account_id',
        'status',
        'icon',
        'color',
        'notes',
        'last_billed_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'billing_date' => 'integer',
        'last_billed_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get next billing date Carbon instance.
     */
    public function getNextBillingDateAttribute(): Carbon
    {
        $today = Carbon::today();
        $targetDay = min($this->billing_date, 28); // Safe day of month

        if ($this->billing_cycle === 'yearly') {
            $next = Carbon::create($today->year, $today->month, $targetDay);
            if ($next->isPast()) {
                $next->addYear();
            }
            return $next;
        }

        if ($this->billing_cycle === 'weekly') {
            return $today->copy()->addDays(7);
        }

        // Monthly default
        $next = Carbon::create($today->year, $today->month, $targetDay);
        if ($next->isPast() && !$next->isToday()) {
            $next->addMonth();
        }
        return $next;
    }

    /**
     * Get days remaining until next billing.
     */
    public function getDaysRemainingAttribute(): int
    {
        $today = Carbon::today();
        $next = $this->next_billing_date;
        return (int) $today->diffInDays($next, false);
    }

    /**
     * Normalize amount to monthly cost.
     */
    public function getMonthlyEquivalentAttribute(): float
    {
        if ($this->billing_cycle === 'yearly') {
            return round($this->amount / 12, 2);
        }
        if ($this->billing_cycle === 'weekly') {
            return round($this->amount * 4.33, 2);
        }
        return (float) $this->amount;
    }
}
