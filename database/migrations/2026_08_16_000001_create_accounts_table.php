<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. BCA Utama, GoPay, Cash Dompet, Bibit
            $table->enum('type', ['bank', 'ewallet', 'cash', 'investment', 'other'])->default('bank');
            $table->string('account_number')->nullable();
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->string('color')->default('#3B82F6');
            $table->string('icon')->default('wallet');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
