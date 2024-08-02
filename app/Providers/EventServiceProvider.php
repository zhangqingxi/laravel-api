<?php

namespace App\Providers;

use App\Events\ModelDeleteEvent;
use App\Events\ModelLogEvent;
use App\Listeners\ModelDeleteListener;
use App\Listeners\ModelLogListener;
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

        ModelLogEvent::class => [
            ModelLogListener::class,
        ],

        ModelDeleteEvent::class => [
            ModelDeleteListener::class,
        ],
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