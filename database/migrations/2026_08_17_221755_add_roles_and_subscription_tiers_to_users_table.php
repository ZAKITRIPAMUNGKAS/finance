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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email'); // 'admin', 'user'
            $table->string('subscription_tier')->default('trial')->after('role'); // 'trial', 'free', 'pro', 'lifetime'
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_tier');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
            $table->boolean('is_banned')->default(false)->after('subscription_ends_at');
            $table->string('banned_reason')->nullable()->after('is_banned');
            $table->timestamp('last_login_at')->nullable()->after('banned_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'subscription_tier',
                'trial_ends_at',
                'subscription_ends_at',
                'is_banned',
                'banned_reason',
                'last_login_at'
            ]);
        });
    }
};
