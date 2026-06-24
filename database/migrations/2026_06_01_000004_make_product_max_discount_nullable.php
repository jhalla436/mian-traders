<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') return;
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'max_discount_percent')) {
            DB::statement('ALTER TABLE products MODIFY max_discount_percent DECIMAL(5,2) NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') return;
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'max_discount_percent')) {
            DB::statement('ALTER TABLE products MODIFY max_discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0');
        }
    }
};
