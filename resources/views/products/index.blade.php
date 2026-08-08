@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
    <h2 class="mb-4">Daftar Produk</h2>

    <div class="row g-4">
        @forelse ($products as $product)
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="{{ $product['image'] ?? '' }}" class="card-img-top p-3" style="height: 200px; object-fit: contain;" alt="{{ $product['title'] ?? 'Produk' }}">
                    <div class="card-body d-flex flex-column">
                    <h6 class="card-title">{{ $product['title'] ?? 'Unknown' }}</h6>
                    <p class="text-muted small mb-2">${{ $product['price'] ?? 0 }}</p>
                    <div class="mt-auto d-grid gap-2">
                        <a href="{{ route('orders.create', ['product_id' => $product['id']]) }}" class="btn btn-primary btn-sm">Beli Langsung</a>
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100">+ Keranjang</button>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Tidak ada produk tersedia saat ini (kemungkinan API sedang bermasalah).</div>
            </div>
        @endforelse
    </div>
@endsection