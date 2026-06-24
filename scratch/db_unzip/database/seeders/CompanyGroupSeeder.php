<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyGroup;

class CompanyGroupSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key'=>'foam',        'name'=>'Foam',        'sort_order'=>1, 'is_active'=>1],
            ['key'=>'hardware',    'name'=>'Hardware',    'sort_order'=>2, 'is_active'=>1],
            ['key'=>'fabric',      'name'=>'Fabric',      'sort_order'=>3, 'is_active'=>1],
            ['key'=>'spring',      'name'=>'Spring',      'sort_order'=>4, 'is_active'=>1],
            ['key'=>'accessories', 'name'=>'Accessories', 'sort_order'=>5, 'is_active'=>1],
            ['key'=>'other',       'name'=>'Other',       'sort_order'=>6, 'is_active'=>1],
        ];

        foreach ($rows as $r) {
            CompanyGroup::updateOrCreate(
                ['key' => $r['key']],
                ['name'=>$r['name'], 'sort_order'=>$r['sort_order'], 'is_active'=>$r['is_active']]
            );
        }
    }
}
