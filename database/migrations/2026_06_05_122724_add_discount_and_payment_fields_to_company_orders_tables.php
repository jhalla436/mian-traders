<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_orders', function (Blueprint $table) {
            $table->decimal('original_discount', 5, 2)->default(0)->after('goods_total');
            $table->decimal('extra_discount', 5, 2)->default(0)->after('original_discount');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('extra_discount');
            $table->string('payment_status', 20)->default('unpaid')->after('paid_amount');
        });

        Schema::table('company_order_items', function (Blueprint $table) {
            $table->decimal('original_discount', 5, 2)->default(0)->after('qty');
            $table->decimal('extra_discount', 5, 2)->default(0)->after('original_discount');
        });
    }

    public function down(): void
    {
        Schema::table('company_orders', function (Blueprint $table) {
            $table->dropColumn(['original_discount', 'extra_discount', 'paid_amount', 'payment_status']);
        });

        Schema::table('company_order_items', function (Blueprint $table) {
            $table->dropColumn(['original_discount', 'extra_discount']);
        });
    }
};
