<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::insert([
            [
                'account' => 'admin',
                'nickname' => 'Super Admin',
                'email' => 'admin@example.com',
                'phone' => '157xxxxxxxx',
                'password' => Hash::make('123456789'),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'account' => 'Visitor',
                'nickname' => 'Visitor User',
                'email' => 'admin222@example.com',
                'phone' => '158xxxxxxxx',
                'password' => Hash::make('123456789'),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
