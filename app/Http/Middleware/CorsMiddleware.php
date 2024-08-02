<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use Closure;
use Illuminate\Http\Request;

/**
 * 跨域中间件
 * @Auther Qasim
 * @date 2023/6/30
 */
class CorsMiddleware
{
    /**
     * 处理传入请求
     * @param Request $request 请求对象
     * @param Closure $next 下一个中间件
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';

        //允许跨域的域名
        if (in_array($origin, config('cors.allowed_origins'))) {

            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN, X-Requested-With',
                'Access-Control-Max-Age' => '86400',
                'Access-Control-Expose-Methods' => 'Authorization, authenticated',
                'Access-Control-Expose-Headers' => 'Content-Type, Authorization, X-Requested-With',
            ];

            if ($request->getMethod() === "OPTIONS") {

                return json(CommonStatusCodes::CROSS_DOMAIN_REQUEST, message('cross_domain_request'), [], 204, $headers);
            }

            // 设置响应头
            foreach ($headers as $key => $header) {

                $response->headers->set($key, $header);
            }
        }

        return $response;
    }
}
