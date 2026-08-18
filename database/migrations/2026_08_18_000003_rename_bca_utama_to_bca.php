<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('accounts')->where('name', 'BCA Utama')->update(['name' => 'BCA']);
    }

    public function down(): void
    {
        DB::table('accounts')->where('name', 'BCA')->update(['name' => 'BCA Utama']);
    }
};
