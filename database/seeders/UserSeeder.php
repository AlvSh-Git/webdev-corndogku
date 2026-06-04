<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'username' => 'customer',
                'name'     => 'Test Customer',
                'email'    => 'customer@corndog.test',
                'role'     => 'customer',
            ],
            [
                'username' => 'owner',
                'name'     => 'Test Owner',
                'email'    => 'owner@corndog.test',
                'role'     => 'owner',
            ],
            [
                'username' => 'cashier',
                'name'     => 'Test Cashier',
                'email'    => 'cashier@corndog.test',
                'role'     => 'cashier',
            ],
        ];

        foreach ($accounts as $account) {
            DB::table('users')->updateOrInsert(
                ['username' => $account['username']],
                array_merge($account, [
                    'password' => Hash::make('password'),
                    'status'   => 'active',
                ])
            );
        }
    }
}
