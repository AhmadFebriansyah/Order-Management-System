@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
    <h2 class="mb-1">Order #{{ $order->order_number }}</h2>
    <p class="text-muted">Dibuat: {{ $order->created_at->format('d M Y H:i') }}</p>


<p class="mt-2">
    <span class="badge bg-info fs-6">{{ $order->statusLabel() }}</span>
</p>

@if (isset($nextStatus[$order->status]))
    <form method="POST" action="{{ route('orders.updateStatus', $order->order_number) }}" class="mb-4">
        @csrf
        <input type="hidden" name="status" value="{{ $nextStatus[$order->status] }}">
        <button type="submit" class="btn btn-sm btn-outline-primary">
            Update ke: {{ $nextStatus[$order->status] }}
        </button>
    </form>
@endif

    <div class="card mb-4">
        <div class="card-header">Item Order</div>
        <ul class="list-group list-group-flush">
            @foreach ($order->items as $item)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                    <span>${{ number_format($item->subtotal, 2) }}</span>
                </li>
            @endforeach
        </ul>
        <div class="card-body">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
            <div class="d-flex justify-content-between"><span>Ongkir</span><span>${{ number_format($order->shipping_cost, 2) }}</span></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
        </div>
    </div>

    @if ($order->payment)
        <div class="card mb-4">
            <div class="card-header">Pembayaran</div>
            <div class="card-body">
                <p class="mb-1">Status: <strong>{{ $order->payment->status }}</strong></p>
                <p class="mb-1">Reference: {{ $order->payment->external_reference }}</p>
                @if ($order->payment->status === 'PENDING')
                    <p class="text-muted small mb-0">Menunggu konfirmasi pembayaran (simulasi via webhook).</p>
                @endif
            </div>
        </div>
    @endif

    @if ($order->shipment)
        <div class="card mb-4">
            <div class="card-header">Pengiriman</div>
            <div class="card-body">
                <p class="mb-1">Kurir: {{ strtoupper($order->shipment->courier) }} ({{ $order->shipment->service }})</p>
                <p class="mb-1">Status: {{ $order->shipment->statusLabel() ?? $order->shipment->status }}</p>
                @if ($order->shipment->tracking_number)
                    <p class="mb-0">No. Resi: {{ $order->shipment->tracking_number }}</p>
                @endif
            </div>
        </div>
    @endif

    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Kembali ke Riwayat Order</a>
@endsection