<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;

/**
 * 路由中间件
 * @Auther Qasim
 * @date 2023/6/30
 */
class RouteMiddleware
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
        $this->setRouteName($request);

        return $next($request);
    }

    /**
     * 设置路由类型
     * @param Request $request
     * @throws CustomException
     */
    private function setRouteName(Request $request): void
    {
        $routeName = route_type($request);

        if(!$routeName){

            throw new CustomException(message('route_not_found'), CommonStatusCodes::ROUTE_NOT_FOUND);
        }

        $request->attributes->set('route_name', $routeName);
    }
}
