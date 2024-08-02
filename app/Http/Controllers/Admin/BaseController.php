<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

/**
 * 后台控制器基类
 * @Auther Qasim
 * @date 2023/6/29
 */
class BaseController extends Controller
{

    protected Redis|Connection $redis;

    public function __construct()
    {
        $this->redis = Redis::connection('admin_cache');
    }

    /**
     * 获取 `message.admin` 语言包
     *
     * @param string $key 语言包的键
     * @param array $replace 替换的数据
     * @param string|null $locale 语言环境
     * @return string
     */
    protected function getMessage(string $key, array $replace = [], string $locale = null): string
    {
        return message($key, 'admin', $replace, $locale);
    }

    /**
     * 转换自定义排序字段
     * @param string $field
     * @return string
     */
    protected function convertSortField(string $field): string
    {

        $sortField = [
            'createTime' => 'created_at',
            'registerTime' => 'created_at',
            'lastLoginTime' => 'last_login_at',
            'sizeText' => 'size',
        ];

        return $sortField[$field] ?? $field;
    }
}
