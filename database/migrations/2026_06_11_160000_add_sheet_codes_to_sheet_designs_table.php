<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sheet_designs', function (Blueprint $table) {
            $table->text('sheet_codes')->nullable()->after('color_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sheet_designs', function (Blueprint $table) {
            $table->dropColumn('sheet_codes');
        });
    }
};
