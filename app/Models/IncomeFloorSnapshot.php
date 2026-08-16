<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeFloorSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'income_floor_value',
        'cv_value',
        'method_selected',
        'avg_income',
        'std_income',
    ];

    protected $casts = [
        'income_floor_value' => 'float',
        'cv_value' => 'float',
        'avg_income' => 'float',
        'std_income' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
