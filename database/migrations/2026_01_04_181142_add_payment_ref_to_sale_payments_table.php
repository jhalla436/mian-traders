<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_payments', 'payment_ref')) {
                $table->string('payment_ref', 64)->nullable()->after('sale_id');
                $table->index('payment_ref');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            if (Schema::hasColumn('sale_payments', 'payment_ref')) {
                $table->dropIndex(['payment_ref']);
                $table->dropColumn('payment_ref');
            }
        });
    }
};
