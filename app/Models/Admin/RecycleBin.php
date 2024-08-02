<?php

namespace App\Models\Admin;
/**
 * 回收站模型
 *
 * @Auther Qasim
 * @date 2023/7/23
 */
class RecycleBin extends Base
{

    protected $fillable = [
        'admin_id', 'table_name', 'table_id', 'title', 'content'
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * content 入库
     * @param $value
     * @return void
     */
    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
