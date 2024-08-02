<?php

namespace Database\Seeders;

use App\Models\Admin\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $firstMenus = [
            // 一级菜单
            ['id' => 1, 'pid' => null, 'name' => 'dashboard', 'path' => 'dashboard', 'component' => '', 'route' => '', 'icon' => 'ant-design:appstore-filled', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'pid' => null, 'name' => 'setting', 'path' => 'setting', 'component' => '', 'route' => '', 'icon' => 'ant-design:tool-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'pid' => null, 'name' => 'system', 'path' => 'system', 'component' => '', 'route' => '', 'icon' => 'ant-design:setting-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'pid' => null, 'name' => 'resource', 'path' => 'resource', 'component' => '', 'route' => '', 'icon' => 'ant-design:interaction-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'pid' => null, 'name' => 'log', 'path' => 'log', 'component' => '', 'route' => '', 'icon' => 'ant-design:alert-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        Menu::insert($firstMenus);

        $secondsMenus = [
            // 二级菜单
            ['id' => 6, 'pid' => 1, 'name' => 'workbench', 'path' => 'workbench', 'component' => '/dashboard/workbench/index', 'route' => '', 'icon' => 'ant-design:code-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'pid' => 2, 'name' => 'site_setting', 'path' => 'site', 'component' => '/setting/site/index', 'route' => '', 'icon' => 'ant-design:dashboard-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'pid' => 3, 'name' => 'menu_manage', 'path' => 'menu', 'component' => '/system/menu/index', 'route' => '', 'icon' => 'ant-design:bars-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'pid' => 3, 'name' => 'user_manage', 'path' => 'user', 'component' => '/system/user/index', 'route' => '', 'icon' => 'ant-design:user-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'pid' => 3, 'name' => 'role_manage', 'path' => 'role', 'component' => '/system/role/index', 'route' => '', 'icon' => 'ant-design:audit-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'pid' => 4, 'name' => 'recycle_manage', 'path' => 'recycle', 'component' => '/resource/recycle/index', 'route' => '', 'icon' => 'ant-design:rest-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'pid' => 4, 'name' => 'file_manage', 'path' => 'file', 'component' => '/resource/file/index', 'route' => '', 'icon' => 'ant-design:file-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'pid' => 5, 'name' => 'request_log', 'path' => 'request', 'component' => '/log/request/index', 'route' => '', 'icon' => 'ant-design:message-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'pid' => 5, 'name' => 'operation_log', 'path' => 'operation', 'component' => '/log/operation/index', 'route' => '', 'icon' => 'ant-design:bug-outlined', 'visible' => true, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
s        ];

        Menu::insert($secondsMenus);

        $thirdMenus = [
            // 三级菜单
            ['id' => 15, 'pid' => 7, 'name' => 'site_index', 'path' => 'index', 'component' =>  '', 'route' => '/site/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'pid' => 7, 'name' => 'site_update', 'path' => 'update', 'component' =>  '', 'route' => '/site/update', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'pid' => 8, 'name' => 'menu_index', 'path' => 'index', 'component' =>  '', 'route' => '/menu/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'pid' => 8, 'name' => 'menu_update', 'path' => 'update', 'component' =>  '', 'route' => '/menu/add', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'pid' => 8, 'name' => 'menu_add', 'path' => 'add', 'component' =>  '', 'route' => '/menu/add', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'pid' => 8, 'name' => 'menu_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/menu/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'pid' => 9, 'name' => 'user_index', 'path' => 'index', 'component' =>  '', 'route' => '/user/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'pid' => 9, 'name' => 'user_update', 'path' => 'update', 'component' =>  '', 'route' => '/user/update', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'pid' => 9, 'name' => 'user_add', 'path' => 'add', 'component' =>  '', 'route' => '/user/add', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'pid' => 9, 'name' => 'user_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/user/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'pid' => 9, 'name' => 'user_batch', 'path' => 'batch', 'component' =>  '', 'route' => '/user/batch', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'pid' => 10, 'name' => 'role_index', 'path' => 'index', 'component' =>  '', 'route' => '/role/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'pid' => 10, 'name' => 'role_update', 'path' => 'update', 'component' =>  '', 'route' => '/role/update', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'pid' => 10, 'name' => 'role_add', 'path' => 'add', 'component' =>  '', 'route' => '/role/add', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'pid' => 10, 'name' => 'role_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/role/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'pid' => 10, 'name' => 'role_batch', 'path' => 'delete', 'component' =>  '', 'route' => '/role/batch', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'pid' => 11, 'name' => 'recycle_index', 'path' => 'index', 'component' =>  '', 'route' => '/recycle/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'pid' => 11, 'name' => 'recycle_restore', 'path' => 'restore', 'component' =>  '', 'route' => '/recycle/restore', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'pid' => 11, 'name' => 'recycle_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/recycle/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'pid' => 11, 'name' => 'recycle_batch', 'path' => 'batch', 'component' =>  '', 'route' => '/recycle/batch', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'pid' => 12, 'name' => 'file_index', 'path' => 'index', 'component' =>  '', 'route' => '/file/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'pid' => 12, 'name' => 'file_update', 'path' => 'update', 'component' =>  '', 'route' => '/file/update', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'pid' => 12, 'name' => 'file_upload', 'path' => 'upload', 'component' =>  '', 'route' => '/file/upload', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'pid' => 12, 'name' => 'file_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/file/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'pid' => 12, 'name' => 'file_batch', 'path' => 'batch', 'component' =>  '', 'route' => '/file/batch', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'pid' => 12, 'name' => 'file_unused', 'path' => 'unused', 'component' =>  '', 'route' => '/file/unused', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'pid' => 13, 'name' => 'request_log_index', 'path' => 'index', 'component' =>  '', 'route' => '/log/request/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 42, 'pid' => 13, 'name' => 'request_log_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/log/request/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 43, 'pid' => 14, 'name' => 'operation_log_index', 'path' => 'index', 'component' =>  '', 'route' => '/log/operation/index', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 44, 'pid' => 14, 'name' => 'operation_log_delete', 'path' => 'delete', 'component' =>  '', 'route' => '/log/operation/delete', 'icon' => '', 'visible' => false, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];

        Menu::insert($thirdMenus);
    }
}
