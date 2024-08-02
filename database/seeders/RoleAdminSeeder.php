<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use App\Models\Admin\RoleAdmin;
use Illuminate\Database\Seeder;

class RoleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = Admin::where('account', 'admin')->first();
        $visitorUser = Admin::where('account', 'visitor')->first();

        $adminUser->roles()->attach(1, ['created_at' => now(), 'updated_at' => now()]);
        $visitorUser->roles()->attach(2, ['created_at' => now(), 'updated_at' => now()]);
    }
}
