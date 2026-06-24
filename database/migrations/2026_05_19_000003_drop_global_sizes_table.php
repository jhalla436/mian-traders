<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('global_sizes');
    }

    public function down(): void
    {
        Schema::create('global_sizes', function (Blueprint $table) {
            $table->id();
            $table->decimal('length_in', 8, 2);
            $table->decimal('width_in', 8, 2);
            $table->decimal('height_in', 8, 2);
            $table->string('name')->nullable();
            $table->timestamps();
            $table->unique(['length_in', 'width_in', 'height_in']);
        });
    }
};
