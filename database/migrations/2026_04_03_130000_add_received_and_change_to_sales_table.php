<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'received_amount')) {
                $table->decimal('received_amount', 12, 2)->default(0)->after('paid_amount');
            }

            if (! Schema::hasColumn('sales', 'change_returned')) {
                $table->decimal('change_returned', 12, 2)->default(0)->after('received_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'change_returned')) {
                $table->dropColumn('change_returned');
            }

            if (Schema::hasColumn('sales', 'received_amount')) {
                $table->dropColumn('received_amount');
            }
        });
    }
};
