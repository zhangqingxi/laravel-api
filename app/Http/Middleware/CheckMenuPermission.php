<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use App\Models\Admin\Menu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


/**
 * 菜单权限中间件
 * @Auther Qasim
 * @date 2023/6/30
 */
class CheckMenuPermission
{
    /**
     * 处理传入请求
     * @param Request $request 请求对象
     * @param Closure $next 下一个中间件
     * @return mixed
     * @throws CustomException
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::guard('admin')->user();

        $routeName = $request->attributes->get('route_name');

        $route = str_replace(['.', $routeName], ['/', ''], $request->route()->getName());

        $menu = Menu::where('route', $route)->first();

        //没有获取到菜单
        if (!$menu) {

            throw new CustomException(message('route_not_found'), CommonStatusCodes::ROUTE_NOT_FOUND);
        }

        $menuRoles = $menu->roles;

        $userRoles = $user->roles;

        // 检查用户的角色和菜单项的角色是否有交集
        if (!$userRoles->intersect($menuRoles)->count()) {

            throw new CustomException(message('route_not_permission'), CommonStatusCodes::ROUTE_NOT_PERMISSION);
        }

        return $next($request);
    }
}
