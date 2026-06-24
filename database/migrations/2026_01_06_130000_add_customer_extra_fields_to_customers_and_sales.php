<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // customers table
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'phone_primary')) $table->string('phone_primary', 30)->nullable()->after('id');
                if (!Schema::hasColumn('customers', 'phone_alt_1'))   $table->string('phone_alt_1', 30)->nullable()->after('phone_primary');
                if (!Schema::hasColumn('customers', 'phone_alt_2'))   $table->string('phone_alt_2', 30)->nullable()->after('phone_alt_1');
                if (!Schema::hasColumn('customers', 'address'))       $table->string('address', 255)->nullable()->after('phone_alt_2');
                if (!Schema::hasColumn('customers', 'cnic'))          $table->string('cnic', 30)->nullable()->after('address');
            });
        }

        // sales table (snapshot on bill)
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'customer_phone2'))   $table->string('customer_phone2', 30)->nullable()->after('customer_phone');
                if (!Schema::hasColumn('sales', 'customer_phone3'))   $table->string('customer_phone3', 30)->nullable()->after('customer_phone2');
                if (!Schema::hasColumn('sales', 'customer_address'))  $table->string('customer_address', 255)->nullable()->after('customer_phone3');
                if (!Schema::hasColumn('sales', 'customer_cnic'))     $table->string('customer_cnic', 30)->nullable()->after('customer_address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $drops = [];
                foreach (['phone_primary','phone_alt_1','phone_alt_2','address','cnic'] as $c) {
                    if (Schema::hasColumn('customers', $c)) $drops[] = $c;
                }
                if (!empty($drops)) $table->dropColumn($drops);
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $drops = [];
                foreach (['customer_phone2','customer_phone3','customer_address','customer_cnic'] as $c) {
                    if (Schema::hasColumn('sales', $c)) $drops[] = $c;
                }
                if (!empty($drops)) $table->dropColumn($drops);
            });
        }
    }
};
