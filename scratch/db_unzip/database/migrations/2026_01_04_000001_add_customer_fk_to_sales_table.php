<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('sales') || !Schema::hasTable('customers')) return;
        if (!Schema::hasColumn('sales', 'customer_id')) return;

        // If FK already exists, do nothing (avoid migration failure)
        $dbName = DB::getDatabaseName();
        $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', 'sales')
            ->where('COLUMN_NAME', 'customer_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if ($exists) return;

        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // optional
            try {
                $table->dropForeign(['customer_id']);
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
