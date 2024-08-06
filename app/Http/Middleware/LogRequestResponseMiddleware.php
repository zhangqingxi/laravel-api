<?php

namespace App\Http\Middleware;

use App\Events\RequestResponseLogEvent;
use App\Jobs\Admin\ProcessRequestLogJob;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * 日志请求响应中间件
 *
 * @package App\Http\Middleware
 * @autor Qasim
 * @time 2023/6/27 16:06
 */
class LogRequestResponseMiddleware
{

    /**
     * 处理传入的请求
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {

        $response = $next($request);

        // 检查请求是否被重复, 查看日志的请求不需要记录
        if (
            !str_contains($request->route()->getPrefix(), 'log') && // 判断是否为日志请求
            !$request->attributes->get('log_has_been_processed') // 判断日志是否已处理
        ) {

            // 触发日志事件
            event(new RequestResponseLogEvent($request, $response));
        }

        return $response;
    }
}
