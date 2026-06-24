<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Default logins (can be changed later from Users table)
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@mian.local',
                'password' => 'admin1234',
                'role' => 'admin',
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@mian.local',
                'password' => 'manager1234',
                'role' => 'manager',
            ],
            [
                'name' => 'Cashier',
                'email' => 'cashier@mian.local',
                'password' => 'cashier1234',
                'role' => 'cashier',
            ],
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'role' => $u['role'],
                    'is_active' => 1,
                ]
            );

            // Assign shops:
            // - Admin/Manager: all shops
            // - Cashier: first shop
            $shops = Shop::query()->where('is_active', 1)->orderBy('id')->get();
            if (($u['role'] ?? '') === 'cashier') {
                $first = $shops->first();
                if ($first) {
                    $user->shops()->syncWithoutDetaching([$first->id]);
                }
            } else {
                if ($shops->count() > 0) {
                    $user->shops()->syncWithoutDetaching($shops->pluck('id')->all());
                }
            }
        }
    }
}
