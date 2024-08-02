<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 文件关联模型
 *
 * @Auther Qasim
 * @date 2023/7/31
 */
class FileAssociation extends Base
{
    public $timestamps = false; // 禁用时间戳
    protected $fillable = [
        'file_id',
        'model_id',
        'model_name',
    ];

    /**
     * 文件
     * @return BelongsTo
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }
}
