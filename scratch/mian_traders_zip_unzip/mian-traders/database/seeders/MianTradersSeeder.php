<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class MianTradersSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Lamination Sheets', 'unit_type' => 'sheet'],
            ['name' => 'UV Sheets', 'unit_type' => 'sheet'],
            ['name' => 'Lasani Simple', 'unit_type' => 'sheet'],
            ['name' => 'Lasani Lamination', 'unit_type' => 'sheet'],
            ['name' => 'Back Press', 'unit_type' => 'sheet'],
            ['name' => 'Hardware Nails', 'unit_type' => 'unit'],
            ['name' => 'Chip Board', 'unit_type' => 'sheet'],
            ['name' => 'Fabric', 'unit_type' => 'meter'],
            ['name' => 'Foam Accessories', 'unit_type' => 'unit'],
        ];

        foreach ($cats as $c) {
            Category::firstOrCreate(['name' => $c['name']], $c);
        }
    }
}
