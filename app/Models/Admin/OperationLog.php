<?php

namespace App\Models\Admin;
/**
 * 操作日志模型
 *
 * @Auther Qasim
 * @date 2023/7/23
 */
class OperationLog extends Base
{

    protected $fillable = [

        'admin_id', 'url', 'method', 'table_name', 'table_id', 'title', 'content', 'ip'
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

    /**
     * ip_address 获取
     * @return string
     */
    public function getIpAddressAttribute(): string
    {

        if($this->ip === '127.0.0.1'){

            return '内网';
        }

        $ipInfo = ip_address($this->ip);

        if($ipInfo){

            return $ipInfo['country']  . '/' . $ipInfo['city'];
        }
        return '';
    }

}
