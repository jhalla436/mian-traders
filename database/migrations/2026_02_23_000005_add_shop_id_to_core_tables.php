<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // sales
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('user_id')->constrained('shops')->nullOnDelete();
                $table->index(['shop_id','created_at']);
            }
        });

        // purchases
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                $table->index(['shop_id','purchase_date']);
            }
        });

        // expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                $table->index(['shop_id','expense_date']);
            }
        });

        // recurring_expenses
        Schema::table('recurring_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('recurring_expenses', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
            }
        });

        // stock_movements
        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_movements', 'shop_id')) {
                    $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                    $table->index(['shop_id','created_at']);
                }
            });
        }

        // company_orders
        if (Schema::hasTable('company_orders')) {
            Schema::table('company_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('company_orders', 'shop_id')) {
                    $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                }
            });
        }

        // Ensure at least one shop exists and backfill shop_id to 1.
        $shopId = DB::table('shops')->orderBy('id')->value('id');
        if (!$shopId) {
            $shopId = DB::table('shops')->insertGetId([
                'name' => 'Shop 1',
                'address' => null,
                'phone' => null,
                'owner_name' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('sales')->whereNull('shop_id')->update(['shop_id' => $shopId]);
        DB::table('purchases')->whereNull('shop_id')->update(['shop_id' => $shopId]);
        DB::table('expenses')->whereNull('shop_id')->update(['shop_id' => $shopId]);
        if (Schema::hasTable('recurring_expenses')) {
            DB::table('recurring_expenses')->whereNull('shop_id')->update(['shop_id' => $shopId]);
        }
        if (Schema::hasTable('stock_movements')) {
            DB::table('stock_movements')->whereNull('shop_id')->update(['shop_id' => $shopId]);
        }
        if (Schema::hasTable('company_orders')) {
            DB::table('company_orders')->whereNull('shop_id')->update(['shop_id' => $shopId]);
        }
    }

    public function down(): void
    {
        // We won't drop columns in down to avoid data loss in production.
    }
};
