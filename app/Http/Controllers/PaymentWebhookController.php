<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handle(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'status' => 'required|in:SUCCESS,FAILED',
        ]);

        $payment = $this->paymentService->handleWebhook(
            $request->input('reference'),
            $request->input('status'),
            $request->all()
        );

        return response()->json(['message' => 'Webhook processed', 'data' => $payment]);
    }
}