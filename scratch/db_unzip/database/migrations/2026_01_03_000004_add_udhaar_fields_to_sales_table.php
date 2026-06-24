<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->date('due_date')->nullable()->after('note');

            // cash or udhaar
            $table->string('sale_type')->default('cash')->after('note');

            // open / paid
            $table->string('status')->default('paid')->after('sale_type');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['customer_phone', 'due_date', 'sale_type', 'status']);
        });
    }
};
