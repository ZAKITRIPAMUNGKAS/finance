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
     * Check if subscription has already been paid this month / cycle.
     */
    public function getIsPaidThisMonthAttribute(): bool
    {
        if (!$this->last_billed_at) {
            return false;
        }

        $last = Carbon::parse($this->last_billed_at);
        $today = Carbon::today();

        if ($this->billing_cycle === 'yearly') {
            return $last->year === $today->year;
        }

        if ($this->billing_cycle === 'weekly') {
            return $last->diffInDays($today) < 7;
        }

        // Monthly: paid in current month & year
        return $last->month === $today->month && $last->year === $today->year;
    }

    /**
     * Get recorded payment history transactions for this subscription.
     */
    public function getPaymentHistoryAttribute()
    {
        return Transaction::where('user_id', $this->user_id)
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->where('description', 'like', 'Pembayaran Langganan: ' . $this->name . '%')
                  ->orWhere('notes', 'like', '%' . $this->name . '%');
            })
            ->with('account')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(6)
            ->get();
    }
}

