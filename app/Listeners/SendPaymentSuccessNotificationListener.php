<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Jobs\SendOrderNotificationJob;

class SendPaymentSuccessNotificationListener
{
    public function handle(PaymentSucceeded $event)
    {
        SendOrderNotificationJob::dispatch($event->order, 'paid');
    }
}