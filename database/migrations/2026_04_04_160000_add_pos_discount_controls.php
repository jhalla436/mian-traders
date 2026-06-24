<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'max_discount_percent')) {
                    $table->decimal('max_discount_percent', 5, 2)
                        ->default(0)
                        ->after('selling_price_default');
                }
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'subtotal_amount')) {
                    $table->decimal('subtotal_amount', 12, 2)
                        ->default(0)
                        ->after('total_amount');
                }
                if (!Schema::hasColumn('sales', 'item_discount_total')) {
                    $table->decimal('item_discount_total', 12, 2)
                        ->default(0)
                        ->after('subtotal_amount');
                }
                if (!Schema::hasColumn('sales', 'overall_discount_amount')) {
                    $table->decimal('overall_discount_amount', 12, 2)
                        ->default(0)
                        ->after('item_discount_total');
                }
            });
        }

        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_items', 'base_price')) {
                    $table->decimal('base_price', 12, 2)
                        ->default(0)
                        ->after('price');
                }
                if (!Schema::hasColumn('sale_items', 'discount_percent')) {
                    $table->decimal('discount_percent', 5, 2)
                        ->default(0)
                        ->after('base_price');
                }
                if (!Schema::hasColumn('sale_items', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)
                        ->default(0)
                        ->after('discount_percent');
                }
                if (!Schema::hasColumn('sale_items', 'base_line_total')) {
                    $table->decimal('base_line_total', 12, 2)
                        ->default(0)
                        ->after('discount_amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                foreach (['base_line_total', 'discount_amount', 'discount_percent', 'base_price'] as $column) {
                    if (Schema::hasColumn('sale_items', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                foreach (['overall_discount_amount', 'item_discount_total', 'subtotal_amount'] as $column) {
                    if (Schema::hasColumn('sales', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'max_discount_percent')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('max_discount_percent');
            });
        }
    }
};
