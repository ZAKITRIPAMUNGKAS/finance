<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_profile_id',
        'category_id',
        'budget_group_id',
        'priority_tier',
        'target_percentage',
    ];

    protected $casts = [
        'priority_tier' => 'integer',
        'target_percentage' => 'float',
    ];

    public function budgetProfile(): BelongsTo
    {
        return $this->belongsTo(BudgetProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function budgetGroup(): BelongsTo
    {
        return $this->belongsTo(BudgetGroup::class);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority_tier) {
            1 => 'Tier 1 — Critical',
            2 => 'Tier 2 — Essential',
            3 => 'Tier 3 — Discretionary',
            default => 'Tier 2 — Essential',
        };
    }
}
