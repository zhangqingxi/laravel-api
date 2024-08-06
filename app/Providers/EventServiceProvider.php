<?php

namespace App\Providers;

use App\Events\ModelDeleteEvent;
use App\Events\ModelLogEvent;
use App\Events\RequestResponseLogEvent;
use App\Listeners\ModelDeleteListener;
use App\Listeners\ModelLogListener;
use App\Listeners\RequestResponseLogListener;
use App\Observers\ModelObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

//        //模型日志事件
//        ModelLogEvent::class => [
//            ModelLogListener::class,
//        ],
//
//        //模型删除事件
//        ModelDeleteEvent::class => [
//            ModelDeleteListener::class,
//        ],

//        //请求响应日志事件
//        RequestResponseLogEvent::class => [
//            RequestResponseLogListener::class,
//        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //注册观察者
        $observers = config('admin.event.observers');

        foreach ($observers as $observer) {

            $observer::observe(ModelObserver::class);
        }
    }

}
