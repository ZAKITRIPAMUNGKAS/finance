<?php

namespace App\Providers;

use App\Models\PurchaseSaving;
use App\Models\Transaction;
use App\Observers\PurchaseSavingObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PurchaseSaving::observe(PurchaseSavingObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
