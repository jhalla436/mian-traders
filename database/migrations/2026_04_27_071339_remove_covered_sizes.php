<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all category sizes for "Covered" categories
        DB::statement("
            DELETE FROM category_sizes 
            WHERE category_id IN (
                SELECT id FROM categories WHERE name = 'Covered'
            )
        ");

        // Delete all "Covered" categories that were created for companies
        DB::statement("
            DELETE FROM categories WHERE name = 'Covered' AND company_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback is not needed for this removal
    }
};
