<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') return;

        // Products table - most queried in POS
        Schema::table('products', function (Blueprint $table) {
            // Index for active product lookups (POS uses this on every page load)
            if (!$this->hasIndex('products', 'idx_products_active_company')) {
                $table->index(['is_active', 'company_id'], 'idx_products_active_company');
            }
            // Index for name search (LIKE queries)
            if (!$this->hasIndex('products', 'idx_products_name')) {
                $table->index('name', 'idx_products_name');
            }
            // Index for category filtering
            if (!$this->hasIndex('products', 'idx_products_category')) {
                $table->index('category_id', 'idx_products_category');
            }
        });

        // Sales table - dashboard queries
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!$this->hasIndex('sales', 'idx_sales_created_at')) {
                    $table->index('created_at', 'idx_sales_created_at');
                }
                if (Schema::hasColumn('sales', 'shop_id') && !$this->hasIndex('sales', 'idx_sales_shop_created')) {
                    $table->index(['shop_id', 'created_at'], 'idx_sales_shop_created');
                }
            });
        }

        // Shop products table - stock lookups
        if (Schema::hasTable('shop_products')) {
            Schema::table('shop_products', function (Blueprint $table) {
                if (!$this->hasIndex('shop_products', 'idx_shop_products_shop_product')) {
                    $table->index(['shop_id', 'product_id'], 'idx_shop_products_shop_product');
                }
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') return;

        Schema::table('products', function (Blueprint $table) {
            if ($this->hasIndex('products', 'idx_products_active_company')) $table->dropIndex('idx_products_active_company');
            if ($this->hasIndex('products', 'idx_products_name')) $table->dropIndex('idx_products_name');
            if ($this->hasIndex('products', 'idx_products_category')) $table->dropIndex('idx_products_category');
        });

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if ($this->hasIndex('sales', 'idx_sales_created_at')) $table->dropIndex('idx_sales_created_at');
                if ($this->hasIndex('sales', 'idx_sales_shop_created')) $table->dropIndex('idx_sales_shop_created');
            });
        }

        if (Schema::hasTable('shop_products')) {
            Schema::table('shop_products', function (Blueprint $table) {
                if ($this->hasIndex('shop_products', 'idx_shop_products_shop_product')) $table->dropIndex('idx_shop_products_shop_product');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
