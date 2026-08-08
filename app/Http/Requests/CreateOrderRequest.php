<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idempotency_key' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping.destination' => 'required|string',
            'shipping.courier' => 'required|string|in:jne,jnt,sicepat',
        ];
    }
}