<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'category',
        'description',
        'total_revenue',
        'start_date',
        'deadline',
        'completed_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'start_date' => 'date',
        'deadline' => 'date',
        'completed_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(ProjectCost::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Kalkulasi cepat profit & margin
    public function getTotalCostAttribute(): float
    {
        $directCosts = (float) $this->costs()->sum('amount');
        // Include any transaction recorded as expense for this project that doesn't duplicate direct costs
        $txCosts = (float) $this->transactions()->where('type', 'expense')->sum('amount');
        return max($directCosts, $txCosts, $directCosts + 0);
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->total_revenue - $this->total_cost;
    }

    public function getMarginPercentageAttribute(): float
    {
        if ($this->total_revenue <= 0) {
            return 0;
        }
        return round(($this->profit / $this->total_revenue) * 100, 1);
    }

    public function getPaidInvoicesTotalAttribute(): float
    {
        $invPaid = (float) $this->invoices()->where('status', 'paid')->sum('amount');
        $txPaid = (float) $this->transactions()->where('type', 'income')->sum('amount');
        return max($invPaid, $txPaid);
    }

    public function getOutstandingInvoicesTotalAttribute(): float
    {
        $explicitOutstanding = (float) $this->invoices()->whereIn('status', ['sent', 'overdue'])->sum('amount');
        if ($explicitOutstanding > 0) {
            return $explicitOutstanding;
        }
        return max(0, $this->total_revenue - $this->paid_invoices_total);
    }
}
