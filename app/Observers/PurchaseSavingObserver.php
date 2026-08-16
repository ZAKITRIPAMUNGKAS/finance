<?php

namespace App\Observers;

use App\Models\PurchaseSaving;
use App\Models\PurchaseWishlist;

class PurchaseSavingObserver
{
    public function created(PurchaseSaving $saving): void
    {
        $this->syncWishlist($saving->wishlist_id);
    }

    public function updated(PurchaseSaving $saving): void
    {
        $this->syncWishlist($saving->wishlist_id);
    }

    public function deleted(PurchaseSaving $saving): void
    {
        $this->syncWishlist($saving->wishlist_id);
    }

    protected function syncWishlist(int $wishlistId): void
    {
        $wishlist = PurchaseWishlist::find($wishlistId);
        if (!$wishlist) {
            return;
        }

        $totalSaved = (float) $wishlist->savings()->sum('amount');
        $wishlist->saved_amount = $totalSaved;

        if ($totalSaved >= (float) $wishlist->current_price && in_array($wishlist->status, ['planning', 'saving'])) {
            $wishlist->status = 'ready';
        } elseif ($totalSaved > 0 && $wishlist->status === 'planning') {
            $wishlist->status = 'saving';
        } elseif ($totalSaved == 0 && $wishlist->status === 'saving') {
            $wishlist->status = 'planning';
        }

        $wishlist->saveQuietly();
    }
}
