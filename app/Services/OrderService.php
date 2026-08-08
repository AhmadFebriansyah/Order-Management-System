<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\ProductStock;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $orderRepository;
    protected $productAggregationService;
    protected $shippingService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductAggregationService $productAggregationService,
        ShippingService $shippingService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productAggregationService = $productAggregationService;
        $this->shippingService = $shippingService;
    }

    public function createOrder(array $data)
    {
        $existingOrder = $this->orderRepository->findByIdempotencyKey($data['idempotency_key']);
        if ($existingOrder) {
            Log::info('Idempotent request detected, returning existing order', [
                'idempotency_key' => $data['idempotency_key'],
                'order_id' => $existingOrder->id,
            ]);
            return $existingOrder;
        }

        $itemsData = [];
        $subtotal = 0;

        foreach ($data['items'] as $item) {
            $product = $this->productAggregationService->getProductDetail($item['product_id']);

            $price = $product['price'];
            $quantity = $item['quantity'];
            $itemSubtotal = $price * $quantity;
            $subtotal += $itemSubtotal;

            $itemsData[] = [
                'external_product_id' => (string) $item['product_id'],
                'product_name' => $product['title'] ?? $product['name'] ?? 'Unknown Product',
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $itemSubtotal,
            ];
        }

        $shippingResult = $this->shippingService->calculate(
            $data['shipping']['destination'],
            $data['shipping']['courier']
        );
        $shippingCost = $shippingResult['cost'];

        $total = $subtotal + $shippingCost;

        $order = DB::transaction(function () use ($data, $itemsData, $subtotal, $shippingCost, $total, $shippingResult) {
            
            foreach ($itemsData as $itemData) {
                $this->reserveStock($itemData['external_product_id'], $itemData['quantity']);
            }

            $order = $this->orderRepository->create([
                'idempotency_key' => $data['idempotency_key'],
                'user_id' => $data['user_id'] ?? null,
                'status' => 'CREATED',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
            ]);

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            $order->shipment()->create([
                'courier' => $shippingResult['courier'],
                'service' => $shippingResult['service'],
                'cost' => $shippingResult['cost'],
                'status' => 'PENDING',
            ]);

            return $order;
        });

        event(new OrderCreated($order));

        Log::info('Order created', ['order_id' => $order->id, 'order_number' => $order->order_number]);

        return $order->fresh(['items', 'shipment']);
    }

    public function transitionStatus($order, $newStatus)
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \Exception("Cannot transition order from {$order->status} to {$newStatus}");
        }

        $previousStatus = $order->status;

        $this->orderRepository->updateStatus($order->id, $newStatus);

        $this->syncShipmentStatus($order, $newStatus);

        $updatedOrder = $order->fresh(['items', 'shipment', 'payment']);

        event(new OrderStatusUpdated($updatedOrder, $previousStatus));

        Log::info('Order status transitioned', [
            'order_id' => $order->id,
            'from' => $previousStatus,
            'to' => $newStatus,
        ]);

        return $updatedOrder;
    }

    protected function syncShipmentStatus($order, $orderStatus)
    {
        if (!$order->shipment) {
            return;
        }

        $shipmentStatuses = ['PACKING', 'IN_WAREHOUSE', 'ON_DELIVERY', 'DELIVERED'];

        if (in_array($orderStatus, $shipmentStatuses)) {
            $order->shipment->update(['status' => $orderStatus]);
        }
    }

    protected function reserveStock($externalProductId, $quantity)
    {
        // lockForUpdate() mengunci baris ini sampai transaction selesai —
        // request lain yang coba akses baris yang sama akan MENUNGGU, bukan langsung baca data lama
        $stock = ProductStock::lockForUpdate()
            ->firstOrCreate(
                ['external_product_id' => $externalProductId],
                ['quantity' => 100] // default stok simulasi
            );

        if ($stock->quantity < $quantity) {
            throw new \Exception("Insufficient stock for product {$externalProductId}. Available: {$stock->quantity}, requested: {$quantity}");
        }

        $stock->decrement('quantity', $quantity);
    }
}