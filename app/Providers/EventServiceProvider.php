<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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
        \App\Events\OrderCreated::class => [
            \App\Listeners\DispatchPaymentJobListener::class,
            \App\Listeners\SendOrderCreatedNotificationListener::class,
        ],
        \App\Events\PaymentSucceeded::class => [
            \App\Listeners\SendPaymentSuccessNotificationListener::class,
        ],
        \App\Events\PaymentFailed::class => [
            \App\Listeners\SendPaymentFailedNotificationListener::class,
        ],
        \App\Events\OrderStatusUpdated::class => [
            \App\Listeners\SendStatusEmailListener::class,
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

        //
    }
}
