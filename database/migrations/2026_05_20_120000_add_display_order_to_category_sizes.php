<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('category_sizes', function (Blueprint $table) {
            $table->integer('display_order')->nullable()->after('size_type')->index();
        });

        // Populate display_order per category using desired ordering: height, length, width
        $categoryIds = DB::table('categories')->pluck('id');
        foreach ($categoryIds as $cid) {
            $sizes = DB::table('category_sizes')
                ->where('category_id', $cid)
                ->orderBy('height_in')
                ->orderBy('length_in')
                ->orderBy('width_in')
                ->get();

            $i = 1;
            foreach ($sizes as $s) {
                DB::table('category_sizes')->where('id', $s->id)->update(['display_order' => $i]);
                $i++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_sizes', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
