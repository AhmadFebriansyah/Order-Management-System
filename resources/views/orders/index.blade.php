@extends('layouts.app')

@section('title', 'Riwayat Order')

@section('content')
    <h2 class="mb-4">Riwayat Order</h2>

    <table class="table bg-white">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Status</th>
                <th>Total</th>
                <th>Tanggal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td class="small">{{ Str::limit($order->order_number, 13) }}</td>
                    <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                    <td>${{ number_format($order->total, 2) }}</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td><a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Belum ada order.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $orders->links() }}
@endsection