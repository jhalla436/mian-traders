<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('default_original_discount', 5, 2)->default(0)->after('max_discount_percent');
            $table->decimal('default_extra_discount', 5, 2)->default(0)->after('default_original_discount');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['default_original_discount', 'default_extra_discount']);
        });
    }
};
