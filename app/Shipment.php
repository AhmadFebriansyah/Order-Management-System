<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id', 'courier', 'service', 'cost', 'tracking_number', 'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function statusLabel()
    {
        $labels = [
            'PENDING' => 'Menunggu Diproses',
            'PACKING' => 'Sedang Dikemas',
            'IN_WAREHOUSE' => 'Sudah di Gudang',
            'ON_DELIVERY' => 'Dalam Pengiriman',
            'DELIVERED' => 'Sudah Sampai',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}