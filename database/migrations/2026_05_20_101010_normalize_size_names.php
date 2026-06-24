<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration normalizes product names that end with a size pattern
     * (e.g., "Product 72×36×4" or "Product 72x36x4" ) into the new
     * canonical format: 72x36x4" (lowercase x and trailing double-quote).
     */
    public function up()
    {
        // Fetch products in manageable chunks to avoid memory issues
        DB::table('products')->orderBy('id')->chunkById(200, function ($products) {
            foreach ($products as $p) {
                $name = $p->name;
                if (!is_string($name) || trim($name) === '') {
                    continue;
                }

                // Match a size at the end of the name. Accept separators: × x X - or spaces, optional trailing quote
                if (preg_match('/(\d+(?:\.\d+)?)(?:[×xX\-\s]+)(\d+(?:\.\d+)?)(?:[×xX\-\s]+)(\d+(?:\.\d+)?)(?:\s*(?:"|”))?$/u', $name, $m)) {
                    [$full, $a, $b, $c] = $m;

                    $fmt = function ($v) {
                        $v = (float)$v;
                        if (floor($v) == $v) return (string)(int)$v;
                        return rtrim(rtrim(number_format($v, 4, '.', ''), '0'), '.');
                    };

                    $nl = $fmt($a);
                    $nw = $fmt($b);
                    $nh = $fmt($c);

                    $replacement = $nl . 'x' . $nw . 'x' . $nh . '\"';

                    // Only update if different
                    if (!str_ends_with($name, $replacement)) {
                        $newName = preg_replace('/(\s*)' . preg_quote($full, '/') . '(?:\s*(?:"|”))?$/u', ' ' . $replacement, $name);
                        DB::table('products')->where('id', $p->id)->update(['name' => $newName]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     * No-op: original formatting may be ambiguous to restore.
     */
    public function down()
    {
        // Intentionally left blank.
    }
};
