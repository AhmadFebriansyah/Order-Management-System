<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ProcessPaymentJob;

class DispatchPaymentJobListener
{
    public function handle(OrderCreated $event)
    {
        ProcessPaymentJob::dispatch($event->order);
    }
}