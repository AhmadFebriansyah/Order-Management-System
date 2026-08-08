<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(CreateOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show($orderNumber)
    {
        $order = app(\App\Repositories\Contracts\OrderRepositoryInterface::class)
            ->findByOrderNumber($orderNumber);

        return response()->json(['data' => $order]);
    }
}