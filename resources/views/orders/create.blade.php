@extends('layouts.app')

@section('title', 'Buat Order')

@section('content')
    <h2 class="mb-4">Checkout</h2>

    @if ($product)
        <div class="card mb-4">
            <div class="card-body">
                <h6>{{ $product['title'] }}</h6>
                <p class="text-muted mb-0">Harga: ${{ $product['price'] }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('orders.store') }}">
        @csrf

        <input type="hidden" name="product_id" value="{{ request('product_id') }}">

        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kota Tujuan</label>
            <input type="text" name="destination" class="form-control" placeholder="Jakarta" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kurir</label>
            <select name="courier" class="form-select" required>
                <option value="jne">JNE</option>
                <option value="jnt">J&T</option>
                <option value="sicepat">SiCepat</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Buat Order</button>
    </form>
@endsection