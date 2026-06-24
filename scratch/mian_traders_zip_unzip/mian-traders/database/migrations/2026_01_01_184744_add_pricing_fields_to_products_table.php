<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'pricing_source')) {
                $table->string('pricing_source')->default('manual');
                // manual | discount_rule
            }

            if (!Schema::hasColumn('products', 'discount_type_id')) {
                $table->foreignId('discount_type_id')->nullable()->constrained('discount_types')->nullOnDelete();
            }

            if (!Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('products', 'purchase_price_manual')) {
                $table->decimal('purchase_price_manual', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('products', 'selling_price_default')) {
                $table->decimal('selling_price_default', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        // keep safe; not dropping for now
    }
};
