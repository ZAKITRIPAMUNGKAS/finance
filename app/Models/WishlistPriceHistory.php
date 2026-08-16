<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'price',
        'recorded_at',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'recorded_at' => 'date',
    ];

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(PurchaseWishlist::class, 'wishlist_id');
    }
}
