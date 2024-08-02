<?php

namespace App\Models\Admin;

/**
 * 角色菜单模型
 *
 * @Auther Qasim
 * @date 2023/6/28
 */
class RoleMenu extends Base
{
    protected $table = 'role_menu';

    public $timestamps = false; // 禁用时间戳

    protected $fillable = [
        'role_id',
        'menu_id',
    ];
}
