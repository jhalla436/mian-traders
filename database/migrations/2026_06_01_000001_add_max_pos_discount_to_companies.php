<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies') && !Schema::hasColumn('companies', 'max_discount_percent')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->decimal('max_discount_percent', 5, 2)
                    ->default(0)
                    ->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'max_discount_percent')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('max_discount_percent');
            });
        }
    }
};
