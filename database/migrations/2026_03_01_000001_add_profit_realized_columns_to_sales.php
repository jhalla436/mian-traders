<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('sales', 'balance_amount')) {
                $table->decimal('balance_amount', 12, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('sales', 'realized_profit')) {
                $table->decimal('realized_profit', 12, 2)->default(0)->after('profit_total');
            }
            if (!Schema::hasColumn('sales', 'unrealized_profit')) {
                $table->decimal('unrealized_profit', 12, 2)->default(0)->after('realized_profit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'unrealized_profit')) $table->dropColumn('unrealized_profit');
            if (Schema::hasColumn('sales', 'realized_profit')) $table->dropColumn('realized_profit');
            // paid_amount/balance_amount might already be used elsewhere, so we keep them.
        });
    }
};