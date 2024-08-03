<?php

use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\DecryptRequestMiddleware;
use App\Http\Middleware\EncryptResponseMiddleware;
use App\Http\Middleware\LogRequestResponseMiddleware;
use App\Http\Middleware\NullToEmptyStringMiddleware;
use App\Http\Middleware\PreventDuplicateRequestsMiddleware;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\RouteMiddleware;
use App\Http\Middleware\ConvertKeysMiddleware;
use App\Http\Middleware\SetLanguageMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('admin', [
            SetLanguageMiddleware::class,  //设置语言包 【第一个执行】
            RouteMiddleware::class,  //路由
            CorsMiddleware::class,  // 跨域
            RateLimitMiddleware::class,  //请求频率限制
            PreventDuplicateRequestsMiddleware::class,  //重复请求
//            VerifyTokenMiddleware::class,  //验证Token === 已由路由文件指定
//            CheckMenuPermission::class, //菜单权限 === 已由路由文件指定
            DecryptRequestMiddleware::class,  //解密请求
            LogRequestResponseMiddleware::class, //记录日志
            EncryptResponseMiddleware::class,  //加密响应
            NullToEmptyStringMiddleware::class, //Null转空
            ConvertKeysMiddleware::class, //最后执行转驼峰式
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e){
//            \Illuminate\Support\Facades\Log::error($e);
            return false;
        });
    })->create();
