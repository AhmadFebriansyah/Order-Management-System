<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusEmailListener implements ShouldQueue
{
    public function handle(OrderStatusUpdated $event)
    {
        $recipient = 'ry44n778@gmail.com';

        if ($event->order->status === 'PAID') {
            Mail::to($recipient)->send(new OrderStatusMail($event->order, 'paid'));
        }

        if ($event->order->status === 'DELIVERED') {
            Mail::to($recipient)->send(new OrderStatusMail($event->order, 'delivered'));
        }
    }
}