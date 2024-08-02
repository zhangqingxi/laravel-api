<?php

use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OperationLogController;
use App\Http\Controllers\Admin\RecycleBinController;
use App\Http\Controllers\Admin\RequestLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\CheckMenuPermission;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

/**
 * 注册管理员路由
 * 主要主处理系统后台API交互
 * @作者 Qasim
 * @日期 2023/6/27
 */
//初始化请求
Route::prefix('init')->get('/admin', [IndexController::class, 'index'])->name('admin.init');

// 正常请求
Route::prefix(config('admin.route_prefix'))->group(function () {

    // 登录
    Route::post('auth/login', [AuthController::class, 'login'])->name('login');

    // 忘记密码
    Route::put('user/forget', [UserController::class, 'forgetPassword'])->name('admin.user.forget');

    //邮件
    Route::prefix('mail')->group(function (){

        //发送邮件
        Route::post('send', [MailController::class, 'send'])->name('admin.mail.send');

        //验证邮件
        Route::post('verify', [MailController::class, 'verify'])->name('admin.mail.verify');
    });

    //API权限验证
    Route::middleware(['auth:sanctum'])->group(function () {

        // 注销登录
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');

        // 刷新Token
        Route::put('auth/refresh', [AuthController::class, 'refresh'])->name('admin.auth.refresh');

        // 用户
        Route::prefix('user')->group(function () {

            // 获取用户信息
            Route::get('info', [UserController::class, 'info'])->name('admin.user.info');

            // 更新用户信息
            Route::put('info', [UserController::class, 'updateInfo'])->name('admin.user.info');

            // 获取用户角色菜单
            Route::get('menus', [UserController::class, 'menus'])->name('admin.user.menus');

            // 更新用户密码
            Route::put('password', [UserController::class, 'updatePassword'])->name('admin.user.password');

            //权限
            Route::middleware([CheckMenuPermission::class])->group(function (){
                // 获取用户列表
                Route::get('/', [UserController::class, 'index'])->name('admin.user.index');

                //更新用户信息
                Route::put('update', [UserController::class, 'update'])->name('admin.user.update');

                //更新用户角色
                Route::put('role', [UserController::class, 'updateRole'])->name('admin.user.role');

                //添加用户
                Route::post('add', [UserController::class, 'add'])->name('admin.user.add');

                //删除用户
                Route::delete('delete', [UserController::class, 'delete'])->name('admin.user.delete');

                //批量操作
                Route::post('batch', [UserController::class, 'batch'])->name('admin.user.batch');
            });
        });

        // 菜单
        Route::middleware([CheckMenuPermission::class])->prefix('menu')->group(function () {

            //菜单列表
            Route::get('/', [MenuController::class, 'index'])->name('admin.menu.index');

            //更新菜单
            Route::put('update', [MenuController::class, 'update'])->name('admin.menu.update');

            //删除菜单
            Route::delete('delete', [MenuController::class, 'delete'])->name('admin.menu.delete');
        });

        // 角色
        Route::middleware([CheckMenuPermission::class])->prefix('role')->group(function () {

            //角色列表
            Route::get('/', [RoleController::class, 'index'])->name('admin.role.index');

            //角色列表
            Route::post('add', [RoleController::class, 'add'])->name('admin.role.add');

            //批量操作
            Route::post('batch', [RoleController::class, 'batch'])->name('admin.role.batch');

            //更新角色
            Route::put('update', [RoleController::class, 'update'])->name('admin.role.update');

            //删除角色
            Route::delete('delete', [RoleController::class, 'delete'])->name('admin.role.delete');
        });

        // 文件
        Route::middleware([CheckMenuPermission::class])->prefix('file')->group(function () {

            //文件列表
            Route::get('/', [FileController::class, 'index'])->name('admin.file.index');

            //文件更新
            Route::put('update', [FileController::class, 'update'])->name('admin.file.update');

            //删除文件
            Route::delete('delete', [FileController::class, 'delete'])->name('admin.file.delete');

            //上传文件
            Route::post('upload', [FileController::class, 'upload'])->name('admin.file.upload');

            //批量文件
            Route::post('batch', [FileController::class, 'batch'])->name('admin.file.batch');

            //取消关联使用
            Route::put('unused', [FileController::class, 'unused'])->name('admin.file.unused');
        });

        // 回收站
        Route::middleware([CheckMenuPermission::class])->prefix('recycle')->group(function () {

            //资源列表
            Route::get('/', [RecycleBinController::class, 'index'])->name('admin.recycle.index');

            //恢复资源
            Route::put('restore', [RecycleBinController::class, 'restore'])->name('admin.recycle.restore');

            //永久删除资源
            Route::delete('delete', [RecycleBinController::class, 'delete'])->name('admin.recycle.delete');

            //批量
            Route::post('batch', [RecycleBinController::class, 'batch'])->name('admin.recycle.batch');
        });

        //日志
        Route::middleware([CheckMenuPermission::class])->prefix('log')->group(function () {

            //请求日志
            Route::prefix('request')->group(function () {

                //请求日志列表
                Route::get('/', [RequestLogController::class, 'index'])->name('admin.log.request.index');

                //请求日志清空
                Route::delete('delete', [RequestLogController::class, 'delete'])->name('admin.log.request.delete');
            });

            //操作日志
            Route::prefix('operation')->group(function () {

                //请求日志列表
                Route::get('/', [OperationLogController::class, 'index'])->name('admin.log.operation.index');

                //请求日志清空
                Route::delete('delete', [OperationLogController::class, 'delete'])->name('admin.log.operation.delete');
            });
        });
    });
});




