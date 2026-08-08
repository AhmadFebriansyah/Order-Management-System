<?php

namespace App\Repositories\Eloquent;

use App\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findByIdempotencyKey($key)
    {
        return $this->model->where('idempotency_key', $key)->first();
    }

    public function findByOrderNumber($orderNumber)
    {
        return $this->model->where('order_number', $orderNumber)
            ->with(['items', 'payment', 'shipment'])
            ->firstOrFail();
    }

    public function updateStatus($orderId, $status)
    {
        return $this->model->where('id', $orderId)->update(['status' => $status]);
    }
}