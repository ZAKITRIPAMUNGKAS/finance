<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->onDelete('set null'); // untuk transfer
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete(); // jika expense/income terkait proyek
            $table->enum('type', ['income', 'expense', 'transfer'])->default('expense');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('description');
            $table->string('reference_number')->nullable();
            $table->string('receipt_image')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('percentage', 5, 2)->default(0); // e.g. 15.00 for 15% of income
            $table->decimal('fixed_amount_limit', 15, 2)->nullable(); // limit alternatif nominal
            $table->integer('period_month')->default(date('n'));
            $table->integer('period_year')->default(date('Y'));
            $table->timestamps();

            $table->unique(['user_id', 'category_id', 'period_month', 'period_year'], 'user_cat_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('transactions');
    }
};
