<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;


/**
 * 基类
 *
 * @Auther Qasim
 * @date 2023/6/28
 */
class Base extends Model
{

    //链接数据库
    protected $connection = 'admin';

    // 基本$casts属性
    protected array $baseCasts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // 基本$hidden属性
    protected array $baseHidden = [
        'pivot',
        'deleted_at'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->mergeValues();
    }

    /**
     * 合并 基类 属性
     * @作者 Qasim
     * @日期 2023/6/28
     */
    public function mergeValues(): void
    {
        $this->casts = array_merge($this->baseHidden, $this->hidden);
        $this->hidden = array_merge($this->baseCasts, $this->casts);
    }

}
