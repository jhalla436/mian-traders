<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->decimal('stock_qty', 12, 2)->default(0);
            $table->decimal('avg_cost', 14, 2)->default(0);
            $table->decimal('selling_price', 14, 2)->nullable();
            $table->decimal('low_stock_alert_qty', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['shop_id','product_id']);
            $table->index(['shop_id','product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
