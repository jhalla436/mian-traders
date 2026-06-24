<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->id();

            // Universal scope: company/category/product
            $table->string('scope_type'); // company | category | product
            $table->unsignedBigInteger('scope_id');

            $table->foreignId('discount_type_id')->constrained('discount_types')->cascadeOnDelete();

            // percent_once | percent_twostep | fixed_purchase | none
            $table->string('rule_type')->default('percent_once');

            $table->decimal('percent_1', 8, 3)->nullable();
            $table->decimal('percent_2', 8, 3)->nullable();
            $table->decimal('fixed_purchase_price', 12, 2)->nullable();

            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['scope_type', 'scope_id']);
            $table->unique(['scope_type', 'scope_id', 'discount_type_id'], 'uniq_rule_per_scope_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_rules');
    }
};
