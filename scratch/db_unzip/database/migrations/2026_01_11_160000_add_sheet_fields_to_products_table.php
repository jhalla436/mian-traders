<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sheet_full_w')) {
                $table->decimal('sheet_full_w', 10, 2)->nullable()->after('height_in');
            }
            if (!Schema::hasColumn('products', 'sheet_full_l')) {
                $table->decimal('sheet_full_l', 10, 2)->nullable()->after('sheet_full_w');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sheet_full_w')) {
                $table->dropColumn('sheet_full_w');
            }
            if (Schema::hasColumn('products', 'sheet_full_l')) {
                $table->dropColumn('sheet_full_l');
            }
        });
    }
};
