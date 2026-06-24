<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'name')) {
                $table->string('name', 160)->nullable()->after('id');
            }
            if (!Schema::hasColumn('shops', 'address')) {
                $table->string('address', 255)->nullable();
            }
            if (!Schema::hasColumn('shops', 'phone')) {
                $table->string('phone', 40)->nullable();
            }
            if (!Schema::hasColumn('shops', 'owner_name')) {
                $table->string('owner_name', 160)->nullable();
            }
            if (!Schema::hasColumn('shops', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            foreach (['name','address','phone','owner_name','is_active'] as $col) {
                if (Schema::hasColumn('shops', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
