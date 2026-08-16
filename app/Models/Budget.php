<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'percentage',
        'fixed_amount_limit',
        'period_month',
        'period_year',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_amount_limit' => 'decimal:2',
        'period_month' => 'integer',
        'period_year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
