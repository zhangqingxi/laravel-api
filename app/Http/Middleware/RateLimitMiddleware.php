<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;

/**
 * 请求频率限制中间件
 * @Auther Qasim
 * @date 2023/6/30
 */
class RateLimitMiddleware
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

        //获取路由类型
        $routeName = $request->attributes->get('route_name');

        // 基于IP地址限制请求次数
        $key = 'rate_limit:' . $request->ip();

        // 请求次数
        $maxAttempts = config($routeName . '.request.max_requests');

        //时间间隔
        $decaySeconds = config($routeName . '.request.time_limit') * 60; //转换为秒

        $redis = redis($routeName);

        if ($attempts = $redis->get($key)) {

            if ($attempts >= $maxAttempts) {

                throw new CustomException(message('too_many_requests'), CommonStatusCodes::TOO_MANY_REQUESTS);
            }

            $redis->incr($key);
        } else {

            $redis->set($key, 1);

            $redis->expire($key, $decaySeconds);
        }

        return $next($request);
    }


}
