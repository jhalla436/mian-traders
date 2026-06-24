<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('categories', 'unit_type')) {
                $table->string('unit_type')->default('unit')->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'parent_id')) $table->dropConstrainedForeignId('parent_id');
            if (Schema::hasColumn('categories', 'unit_type')) $table->dropColumn('unit_type');
        });
    }
};
