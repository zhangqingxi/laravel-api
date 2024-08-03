<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;

/**
 * 设置语言包中间件
 * @Auther Qasim
 * @date 2023/7/31
 */
class SetLanguageMiddleware
{
    /**
     * 处理传入请求
     * @param Request $request 请求对象
     * @param Closure $next 下一个中间件
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // 获取 Accept-Language 头部
        $acceptLanguage = $request->header('Accept-Language', 'zh-CN');

        // 解析语言标识
        $preferredLanguage = $this->parsePreferredLanguage($acceptLanguage);

        // 设置应用语言
        app()->setLocale(str_replace('-', '_', $preferredLanguage));

        // 继续处理请求
        return $next($request);
    }

    /**
     * 解析客户端首选的语言标识
     *
     * @param string $acceptLanguage
     * @return string
     */
    private function parsePreferredLanguage(string $acceptLanguage): string
    {
        // 解析 Accept-Language 字符串
        $languages = explode(',', $acceptLanguage);

        // 返回首选语言标识
        return trim(array_shift($languages));
    }

}
