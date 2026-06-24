<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_sizes', function (Blueprint $table) {
            $table->id();
            $table->float('length_in');
            $table->float('width_in');
            $table->float('height_in');
            $table->string('name')->nullable()->comment('Optional custom name');
            $table->timestamps();
            
            // Unique constraint to avoid duplicates
            $table->unique(['length_in', 'width_in', 'height_in']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_sizes');
    }
};
