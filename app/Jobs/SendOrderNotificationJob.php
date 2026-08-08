<?php

namespace App\Jobs;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOrderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $order;
    protected $type; // 'created', 'paid', 'failed'

    public function __construct(Order $order, $type)
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function handle()
    {
        \Log::info('Sending order notification email (simulated)', [
            'order_id' => $this->order->id,
            'type' => $this->type,
        ]);
    }
}