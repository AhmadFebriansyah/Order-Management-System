<?php

namespace App\Clients;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MockPaymentClient
{
    public function createPaymentRequest($orderId, $amount)
    {
        $start = microtime(true);

        $reference = 'PAY-' . Str::upper(Str::random(10));

        Log::info('External API call (simulated): MockPayment createPaymentRequest', [
            'order_id' => $orderId,
            'amount' => $amount,
            'duration_ms' => round((microtime(true) - $start) * 1000),
        ]);

        return [
            'reference' => $reference,
            'payment_url' => "https://mock-payment.test/pay/{$reference}",
            'status' => 'PENDING',
        ];
    }
}