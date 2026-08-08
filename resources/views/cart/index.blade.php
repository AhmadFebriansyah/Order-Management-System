@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
    <h2 class="mb-4">Keranjang Belanja</h2>

    @if (empty($items))
        <p class="text-muted">Keranjang kosong. <a href="{{ route('products.index') }}">Lihat produk</a></p>
    @else
        <table class="table bg-white">
            <thead>
                <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item['title'] }}</td>
                        <td>${{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>${{ number_format($item['subtotal'], 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.remove', $item['product_id']) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <h5>Subtotal: ${{ number_format($subtotal, 2) }}</h5>
            <a href="{{ route('cart.checkout') }}" class="btn btn-success">Checkout</a>
        </div>
    @endif
@endsection