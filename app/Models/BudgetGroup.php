<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'default_priority_tier',
        'icon',
        'color',
        'description',
    ];

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class);
    }
}
