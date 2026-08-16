<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'invoice_number',
        'amount',
        'issue_date',
        'due_date',
        'status',
        'paid_at',
        'paid_to_account_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function paidToAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'paid_to_account_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['sent', 'overdue']) && $this->due_date < now()->startOfDay();
    }
}
