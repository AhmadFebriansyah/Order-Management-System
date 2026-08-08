<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif;">
    <h2>
        @if ($type === 'paid')
            Pembayaran Diterima
        @else
            Pesanan Telah Sampai
        @endif
    </h2>

    <p>Order Number: <strong>{{ $order->order_number }}</strong></p>

    <table style="width: 100%; border-collapse: collapse;" border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Subtotal: ${{ number_format($order->subtotal, 2) }}</p>
    <p>Ongkir: ${{ number_format($order->shipping_cost, 2) }}</p>
    <p><strong>Total: ${{ number_format($order->total, 2) }}</strong></p>

    @if ($order->shipment)
        <p>Kurir: {{ strtoupper($order->shipment->courier) }} ({{ $order->shipment->service }})</p>
    @endif
</body>
</html>