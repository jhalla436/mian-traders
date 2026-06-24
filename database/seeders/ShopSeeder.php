<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Shop 1', 'is_active' => 1],
            ['name' => 'Shop 2', 'is_active' => 1],
            ['name' => 'Shop 3', 'is_active' => 1],
        ];

        foreach ($defaults as $s) {
            Shop::updateOrCreate(
                ['name' => $s['name']],
                [
                    'address' => null,
                    'phone' => null,
                    'owner_name' => null,
                    'is_active' => $s['is_active'],
                ]
            );
        }
    }
}
