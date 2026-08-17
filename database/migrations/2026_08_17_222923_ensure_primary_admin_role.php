<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('users')
            ->where('email', 'zakitripamungkas03@gmail.com')
            ->update([
                'role' => 'admin',
                'subscription_tier' => 'lifetime',
                'trial_ends_at' => null,
                'subscription_ends_at' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
