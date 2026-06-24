<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Add only if not exist (safe)
            if (!Schema::hasColumn('products', 'name')) {
                $table->string('name')->after('id');
            }

            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->after('name');
            }

            if (!Schema::hasColumn('products', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('category_id');
            }

            if (!Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 12, 2)->nullable()->after('company_id');
            }

            if (!Schema::hasColumn('products', 'purchase_price_manual')) {
                $table->decimal('purchase_price_manual', 12, 2)->nullable()->after('mrp');
            }

            if (!Schema::hasColumn('products', 'sale_price_default')) {
                $table->decimal('sale_price_default', 12, 2)->nullable()->after('purchase_price_manual');
            }

            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sale_price_default');
            }

            if (!Schema::hasColumn('products', 'stock_qty')) {
                $table->decimal('stock_qty', 12, 2)->default(0)->after('is_active');
            }

            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('stock_qty');
            }

            if (!Schema::hasColumn('products', 'image_path')) {
                $table->string('image_path')->nullable()->after('sku');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Keep down minimal (optional)
        });
    }
};
