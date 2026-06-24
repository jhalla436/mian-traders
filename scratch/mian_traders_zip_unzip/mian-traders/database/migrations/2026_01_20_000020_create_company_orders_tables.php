<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('order_date');
            $table->string('status', 20)->default('open'); // open | received | cancelled
            $table->decimal('goods_total', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('company_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_order_id')->constrained('company_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('qty', 12, 3)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0); // auto by company discount, editable
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_order_items');
        Schema::dropIfExists('company_orders');
    }
};
