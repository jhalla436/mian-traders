<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set products with max_discount_percent = 0 to NULL so they inherit company defaults
        DB::table('products')
            ->where('max_discount_percent', 0)
            ->update(['max_discount_percent' => null]);
    }

    public function down(): void
    {
        // Revert: set NULL values back to 0
        DB::table('products')
            ->whereNull('max_discount_percent')
            ->update(['max_discount_percent' => 0]);
    }
};
