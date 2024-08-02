<?php

namespace App\Providers;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Swoole\Table;
use Swoole\WebSocket\Server;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //注册全局异常
        $this->app->singleton(
            ExceptionHandler::class,
            Handler::class
        );

        // 在应用启动时初始化 Swoole Table
//        $this->app->singleton('userTable', function () {
//            $table = new Table(1024);
//            $table->column('uid', Table::TYPE_INT, 8);
//            $table->column('token_id', Table::TYPE_STRING, 8);
//            $table->column('token_value', Table::TYPE_STRING, 64);
//            $table->create();
//            return $table;
//        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
    }
}
