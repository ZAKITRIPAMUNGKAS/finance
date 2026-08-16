<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PurchaseWishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'target_price',
        'current_price',
        'product_url',
        'priority',
        'target_date',
        'saved_amount',
        'status',
        'purchased_at',
        'notes',
    ];

    protected $casts = [
        'target_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'saved_amount' => 'decimal:2',
        'target_date' => 'date',
        'purchased_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(PurchaseSaving::class, 'wishlist_id');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(WishlistPriceHistory::class, 'wishlist_id')->orderBy('recorded_at', 'desc');
    }

    // Perhitungan otomatis PRD v1.1
    public function getShortageAmountAttribute(): float
    {
        return max(0, (float) $this->current_price - (float) $this->saved_amount);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->current_price <= 0) {
            return 0;
        }
        return min(100, round(((float) $this->saved_amount / (float) $this->current_price) * 100, 1));
    }

    public function getRemainingMonthsAttribute(): int
    {
        if (!$this->target_date) {
            return 1;
        }
        $target = Carbon::parse($this->target_date)->startOfMonth();
        $now = now()->startOfMonth();
        $diff = $now->diffInMonths($target, false);
        return max(1, (int) $diff);
    }

    public function getMonthlySavingNeedAttribute(): float
    {
        $shortage = $this->shortage_amount;
        if ($shortage <= 0) {
            return 0;
        }
        return round($shortage / $this->remaining_months, 2);
    }
}
