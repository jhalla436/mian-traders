<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['shop_id', 'id'], 'sales_shop_id_id_index');
            $table->index(['shop_id', 'balance_amount'], 'sales_shop_id_balance_amount_index');
            $table->index('customer_phone', 'sales_customer_phone_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'company_id', 'name'], 'products_active_company_name_index');
            $table->index('sku', 'products_sku_index');
        });

        Schema::table('leftover_pieces', function (Blueprint $table) {
            $table->index(['is_active', 'qty', 'id'], 'leftover_pieces_active_qty_id_index');
            $table->index(['product_id', 'is_active', 'width_ft', 'length_ft'], 'leftover_pieces_product_active_dims_index');
        });
    }

    public function down(): void
    {
        Schema::table('leftover_pieces', function (Blueprint $table) {
            $table->dropIndex('leftover_pieces_product_active_dims_index');
            $table->dropIndex('leftover_pieces_active_qty_id_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_sku_index');
            $table->dropIndex('products_active_company_name_index');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_customer_phone_index');
            $table->dropIndex('sales_shop_id_balance_amount_index');
            $table->dropIndex('sales_shop_id_id_index');
        });
    }
};
