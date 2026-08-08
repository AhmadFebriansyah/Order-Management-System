<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function create(array $data);
    public function findByIdempotencyKey($key);
    public function findByOrderNumber($orderNumber);
    public function updateStatus($orderId, $status);
}