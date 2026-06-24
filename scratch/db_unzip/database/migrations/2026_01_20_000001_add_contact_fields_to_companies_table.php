<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'phone_main')) {
                $table->string('phone_main', 40)->nullable();
            }
            if (!Schema::hasColumn('companies', 'email')) {
                $table->string('email', 120)->nullable();
            }
            if (!Schema::hasColumn('companies', 'address')) {
                $table->string('address', 255)->nullable();
            }
            if (!Schema::hasColumn('companies', 'note')) {
                $table->text('note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'note')) $table->dropColumn('note');
            if (Schema::hasColumn('companies', 'address')) $table->dropColumn('address');
            if (Schema::hasColumn('companies', 'email')) $table->dropColumn('email');
            if (Schema::hasColumn('companies', 'phone_main')) $table->dropColumn('phone_main');
        });
    }
};
