<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 角色模型
 *
 * @Auther Qasim
 * @date 2023/6/28
 */
class Role extends Base
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'remark',
        'deleted_at'
    ];

    public static function boot(): void
    {

        parent::boot(); // TODO: Change the autogenerated stub

        //当角色被硬删除时，中间表也要删除
        static::deleting(function (Role $model) {
            // forceDeleting 表示硬删除
            if ($model->forceDeleting) {

                $model->menus()->detach();
            }
        });
    }

    /**
     * 角色菜单
     * @return BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menu', 'role_id', 'menu_id');
    }
}
