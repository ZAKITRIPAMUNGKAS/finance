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
        return (float) $this->costs()->sum('amount');
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
        return (float) $this->invoices()->where('status', 'paid')->sum('amount');
    }

    public function getOutstandingInvoicesTotalAttribute(): float
    {
        return (float) $this->invoices()->whereIn('status', ['sent', 'overdue'])->sum('amount');
    }
}
