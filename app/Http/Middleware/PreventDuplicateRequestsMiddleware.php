<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;

/**
 * 防止重复请求中间件
 * @Auther Qasim
 * @date 2023/6/30
 */
class PreventDuplicateRequestsMiddleware
{
    /**
     * 处理传入请求
     * @param Request $request 请求对象
     * @param Closure $next 下一个中间件
     * @return mixed
     * @throws CustomException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        //如果请求路由是DELETE、POST、PUT
        if(in_array($request->getMethod(), ['DELETE', 'POST', 'PUT'])){
            //获取路由类型
            $routeName = $request->attributes->get('route_name');

            // 通过请求参数构建唯一 ID
            $key = 'duplicate_request:' . sha1(json_encode($request->all()));

            $redis = redis($routeName);

            //时间间隔
            $decaySeconds = config($routeName . '.duplicate_time');

            // 检查请求 ID 是否存在
            if ($redis->get($key)) {

                throw new CustomException(message('request_duplicate'), CommonStatusCodes::REQUEST_DUPLICATE);
            }

            // 将请求 ID 存储到 Cache 中，并设置过期时间
            $redis->set($key, true);
            $redis->expire($key, $decaySeconds);
        }
        return $next($request);
    }
}
