<?php

namespace App\Jobs;

use App\Order;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;       // retry maksimal 3x kalau gagal
    public $backoff = 10;    // tunggu 10 detik sebelum retry

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(PaymentService $paymentService)
    {
        $paymentService->initiatePayment($this->order);
    }

    public function failed(\Exception $exception)
    {
        \Log::error('ProcessPaymentJob failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}