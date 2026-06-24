<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration updates existing NULL size_type values to 'standard'
     * and alters the column to be NOT NULL with default 'standard'.
     */
    public function up()
    {
        // Ensure existing rows have a value
        DB::table('category_sizes')->whereNull('size_type')->orWhere('size_type', '')->update(['size_type' => 'standard']);

        // Alter the column to have a non-null default. Use raw statement for compatibility.
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `category_sizes` MODIFY `size_type` VARCHAR(255) NOT NULL DEFAULT 'standard';");
        } else {
            // Fallback: attempt schema change (requires doctrine/dbal)
            Schema::table('category_sizes', function (Blueprint $table) {
                $table->string('size_type')->default('standard')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to nullable without default
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `category_sizes` MODIFY `size_type` VARCHAR(255) NULL DEFAULT NULL;");
        } else {
            Schema::table('category_sizes', function (Blueprint $table) {
                $table->string('size_type')->nullable()->default(null)->change();
            });
        }
    }
};
