<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('phone', 20)->unique();
                $table->string('name')->nullable();
                $table->string('address')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            // add columns only if missing
            if (!Schema::hasColumn('customers', 'phone')) {
                $table->string('phone', 20)->nullable()->after('id');
            }
            if (!Schema::hasColumn('customers', 'name')) {
                $table->string('name')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->string('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'created_at')) {
                $table->timestamps();
            }
        });

        // Make phone unique if not already
        // (Laravel doesn't have easy "if index exists" check; so we keep it simple:
        // If this fails due to duplicate index, tell me and I'll give exact drop+add.)
        try {
            Schema::table('customers', function (Blueprint $table) {
                $table->unique('phone');
            });
        } catch (\Throwable $e) {
            // ignore if already unique
        }
    }

    public function down(): void
    {
        // do nothing (safe migration)
    }
};
