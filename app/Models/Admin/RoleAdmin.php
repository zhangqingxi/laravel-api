<?php

namespace App\Models\Admin;

/**
 * 角色管理员模型
 *
 * @Auther Qasim
 * @date 2023/6/28
 */
class RoleAdmin extends Base
{
    protected $table = 'role_admin';

    public $timestamps = false; // 禁用时间戳

    protected $fillable = [
        'role_id',
        'admin_id',
    ];
}
