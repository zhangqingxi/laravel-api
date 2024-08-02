<?php

namespace Database\Seeders;

use App\Models\Admin\Menu;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Admin;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'Visitor')->first();

        $menus = Menu::all();

        foreach ($menus as $menu) {

            $adminRole->menus()->attach($menu->id, ['created_at' => now(), 'updated_at' => now()]);
        }

        $userMenu = Menu::where('name', 'Dashboard')->first();

        $userRole->menus()->attach($userMenu->id, ['created_at' => now(), 'updated_at' => now()]);
    }
}
