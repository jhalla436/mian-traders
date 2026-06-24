<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'pricing_mode')) {
                // discount | manual | shell_rate
                $table->string('pricing_mode')->default('discount')->after('id');
            }

            if (!Schema::hasColumn('products', 'shell_rate')) {
                $table->decimal('shell_rate', 10, 2)->nullable()->after('pricing_mode');
            }

            if (!Schema::hasColumn('products', 'width_in')) {
                $table->decimal('width_in', 10, 2)->nullable()->after('shell_rate');
            }

            if (!Schema::hasColumn('products', 'length_in')) {
                $table->decimal('length_in', 10, 2)->nullable()->after('width_in');
            }

            if (!Schema::hasColumn('products', 'height_in')) {
                $table->decimal('height_in', 10, 2)->nullable()->after('length_in');
            }
        });
    }

    public function down(): void
    {
        // keep safe (do nothing)
    }
};
