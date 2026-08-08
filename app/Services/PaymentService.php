<?php

namespace App\Services;

use App\Order;
use App\Payment;
use App\Clients\MockPaymentClient;
use App\Events\PaymentSucceeded;
use App\Events\PaymentFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $paymentClient;
    protected $orderService;

    public function __construct(MockPaymentClient $paymentClient, OrderService $orderService)
    {
        $this->paymentClient = $paymentClient;
        $this->orderService = $orderService;
    }

    public function initiatePayment(Order $order)
    {
        $result = $this->paymentClient->createPaymentRequest($order->id, $order->total);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'mock',
            'amount' => $order->total,
            'status' => 'PENDING',
            'external_reference' => $result['reference'],
        ]);

        $this->orderService->transitionStatus($order, 'PENDING_PAYMENT');

        Log::info('Payment initiated', ['order_id' => $order->id, 'reference' => $result['reference']]);

        return $payment;
    }

    public function handleWebhook($reference, $status, $rawPayload = null)
    {
        $payment = Payment::where('external_reference', $reference)->firstOrFail();

        if ($payment->status !== 'PENDING') {
            Log::info('Webhook already processed, skipping', ['reference' => $reference]);
            return $payment;
        }

        DB::transaction(function () use ($payment, $status, $rawPayload) {
            $payment->update([
                'status' => $status,
                'raw_payload' => json_encode($rawPayload),
                'paid_at' => $status === 'SUCCESS' ? now() : null,
            ]);
        });

        $order = $payment->order;

        if ($status === 'SUCCESS') {
            $order = $this->orderService->transitionStatus($order, 'PAID');
            event(new PaymentSucceeded($order));
        } else {
            $order = $this->orderService->transitionStatus($order, 'FAILED');
            event(new PaymentFailed($order));
        }

        Log::info('Payment webhook processed', ['reference' => $reference, 'status' => $status]);

        // Refresh payment supaya relasi 'order' di dalamnya ikut ter-update dengan data terbaru
        return $payment->fresh(['order']);
    }
}