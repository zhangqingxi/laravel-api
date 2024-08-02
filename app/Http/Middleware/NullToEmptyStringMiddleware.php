<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;

/**
 * NUll转空字符串中间件
 * @Auther Qasim
 * @date 2023/7/6
 */
class NullToEmptyStringMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws CustomException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        //响应数据
        $responseData = $response->getData(true);

        //处理null转空字符串
        $data = transform_null_to_empty_string($responseData['data']);

        $responseData['data'] = $data;

        $response->setData($responseData);

        return $response;
    }
}
