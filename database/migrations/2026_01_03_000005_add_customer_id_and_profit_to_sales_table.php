<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('sales')) return;

        Schema::table('sales', function (Blueprint $table) {
            // Link sale to customers table (nullable)
            if (!Schema::hasColumn('sales', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
                $table->index('customer_id');
            }

            // Profit columns used by dashboard and reports
            if (!Schema::hasColumn('sales', 'profit_total')) {
                $table->decimal('profit_total', 12, 2)->default(0)->after('balance_amount');
            }
            if (!Schema::hasColumn('sales', 'profit_realized')) {
                $table->decimal('profit_realized', 12, 2)->default(0)->after('profit_total');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sales')) return;

        Schema::table('sales', function (Blueprint $table) {
            $drops = [];
            foreach (['profit_realized','profit_total'] as $c) {
                if (Schema::hasColumn('sales', $c)) $drops[] = $c;
            }
            if (!empty($drops)) $table->dropColumn($drops);

            if (Schema::hasColumn('sales', 'customer_id')) {
                try { $table->dropIndex(['customer_id']); } catch (\Throwable $e) {}
                $table->dropColumn('customer_id');
            }
        });
    }
};
