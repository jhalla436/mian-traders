<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('ref_type')->nullable()->after('qty');   // sale, adjustment, etc
            $table->unsignedBigInteger('ref_id')->nullable()->after('ref_type');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['ref_type', 'ref_id']);
        });
    }
};
