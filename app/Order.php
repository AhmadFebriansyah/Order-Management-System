<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'idempotency_key', 'user_id', 'status',
        'subtotal', 'shipping_cost', 'total', 'paid_at',
    ];

    protected $dates = ['paid_at'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = (string) Uuid::uuid4();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    // Helper untuk validasi transisi status (state machine sederhana)
    public function canTransitionTo($newStatus)
    {
        $allowed = [
            'CREATED'         => ['PENDING_PAYMENT', 'CANCELLED'],
            'PENDING_PAYMENT' => ['PAID', 'FAILED', 'CANCELLED'],
            'PAID'            => ['PACKING'],
            'PACKING'         => ['IN_WAREHOUSE'],
            'IN_WAREHOUSE'    => ['ON_DELIVERY'],
            'ON_DELIVERY'     => ['DELIVERED'],
        ];

        return isset($allowed[$this->status])
            && in_array($newStatus, $allowed[$this->status]);
    }

    public function statusLabel()
    {
        $labels = [
            'CREATED' => 'Order Dibuat',
            'PENDING_PAYMENT' => 'Menunggu Pembayaran',
            'PAID' => 'Sudah Dibayar',
            'PACKING' => 'Sedang Dikemas',
            'IN_WAREHOUSE' => 'Sudah di Gudang',
            'ON_DELIVERY' => 'Dalam Pengiriman',
            'DELIVERED' => 'Pesanan Sampai',
            'FAILED' => 'Gagal',
            'CANCELLED' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}