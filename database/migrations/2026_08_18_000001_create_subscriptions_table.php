<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, weekly
            $table->unsignedTinyInteger('billing_date')->default(1); // 1-31
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active'); // active, paused, cancelled
            $table->string('icon')->nullable()->default('repeat');
            $table->string('color')->nullable()->default('#0F172A');
            $table->text('notes')->nullable();
            $table->date('last_billed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
