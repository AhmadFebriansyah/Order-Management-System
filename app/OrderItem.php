<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'external_product_id', 'product_name', 'price', 'quantity', 'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}