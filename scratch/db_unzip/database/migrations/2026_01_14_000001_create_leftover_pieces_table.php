<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leftover_pieces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();

            // remaining rectangle
            $table->decimal('width_ft', 10, 4)->default(0);
            $table->decimal('length_ft', 10, 4)->default(0);

            // how many same leftover rectangles (usually 1)
            $table->decimal('qty', 10, 4)->default(1);

            $table->boolean('is_active')->default(true);
            $table->string('note', 255)->nullable();

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leftover_pieces');
    }
};
