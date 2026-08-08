<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Jobs\SendOrderNotificationJob;

class SendPaymentFailedNotificationListener
{
    public function handle(PaymentFailed $event)
    {
        SendOrderNotificationJob::dispatch($event->order, 'failed');
    }
}