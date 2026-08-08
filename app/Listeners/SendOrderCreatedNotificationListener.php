<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendOrderNotificationJob;

class SendOrderCreatedNotificationListener
{
    public function handle(OrderCreated $event)
    {
        SendOrderNotificationJob::dispatch($event->order, 'created');
    }
}