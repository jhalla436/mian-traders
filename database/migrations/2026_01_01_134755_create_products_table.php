<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->nullable()->constrained('companies');
      $table->foreignId('category_id')->constrained('categories');

      $table->string('name'); // REQUIRED
      $table->boolean('is_variant_parent')->default(false); // REQUIRED

      $table->decimal('mrp', 12, 2)->nullable();
      $table->decimal('purchase_price_manual', 12, 2)->nullable();
      $table->decimal('selling_price_default', 12, 2)->nullable();

      $table->boolean('is_split_parent')->default(false);
      $table->unsignedInteger('split_total_parts')->nullable();

      $table->integer('low_stock_alert_qty')->default(5);
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('products');
  }
};
