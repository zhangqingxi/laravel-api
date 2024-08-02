<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Observers\ModelObserver;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);

        //注册观察者
        $observers = config('admin.event.observers');

        foreach ($observers as $observer) {

            $observer::observe(ModelObserver::class);
        }
    }
}
