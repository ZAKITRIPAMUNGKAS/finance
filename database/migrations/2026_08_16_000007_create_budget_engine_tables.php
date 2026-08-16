<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Budget Groups (Fixed System, 6 Categories)
        Schema::create('budget_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('default_priority_tier')->default(2); // 1, 2, 3
            $table->string('icon')->default('folder');
            $table->string('color')->default('#64748B');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Budget Profiles ("Bulan Normal", "Bulan Kering", dll.)
        Schema::create('budget_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('method')->default('floor'); // 'floor' or 'average'
            $table->timestamps();
        });

        // 3. Budget Categories (User-Configurable Category Mapping to Group & Priority Tier)
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_group_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('priority_tier')->default(2); // 1 = Critical, 2 = Essential, 3 = Discretionary
            $table->decimal('target_percentage', 5, 2)->default(0); // e.g. 15.50%
            $table->timestamps();
        });

        // 4. Income Floor Snapshots (Audit Trail & Rolling Historical Data)
        Schema::create('income_floor_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('month', 7); // 'YYYY-MM'
            $table->decimal('income_floor_value', 15, 2)->default(0); // P25 value
            $table->decimal('cv_value', 8, 4)->default(0); // Coefficient of Variation
            $table->string('method_selected')->default('floor'); // 'floor' or 'average'
            $table->decimal('avg_income', 15, 2)->default(0);
            $table->decimal('std_income', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_floor_snapshots');
        Schema::dropIfExists('budget_categories');
        Schema::dropIfExists('budget_profiles');
        Schema::dropIfExists('budget_groups');
    }
};
