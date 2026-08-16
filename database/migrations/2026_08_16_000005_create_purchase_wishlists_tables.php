<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. DJI Pocket 4, SSD 2TB, Monitor 27 inch 4K
            $table->string('category')->default('Alat Kerja'); // Alat Kerja, Gadget, Hobi, Kendaraan, dll
            $table->decimal('target_price', 15, 2);
            $table->decimal('current_price', 15, 2);
            $table->string('product_url')->nullable();
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->date('target_date')->nullable();
            $table->decimal('saved_amount', 15, 2)->default(0);
            $table->enum('status', ['planning', 'saving', 'ready', 'purchased', 'cancelled'])->default('planning');
            $table->timestamp('purchased_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained('purchase_wishlists')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('wishlist_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained('purchase_wishlists')->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->date('recorded_at');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_price_histories');
        Schema::dropIfExists('purchase_savings');
        Schema::dropIfExists('purchase_wishlists');
    }
};
