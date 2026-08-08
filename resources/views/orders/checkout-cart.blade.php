@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <h2 class="mb-4">Checkout</h2>

    <form method="POST" action="{{ route('cart.checkout.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kota Tujuan</label>
            <input type="text" name="destination" class="form-control" placeholder="Jakarta" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kurir</label>
            <select name="courier" class="form-select" required>
                <option value="jne">JNE — $1.00</option>
                <option value="jnt">J&T — $1.20</option>
                <option value="sicepat">SiCepat — $1.30</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Buat Order</button>
    </form>
@endsection