<?php

namespace App\Http\Controllers;

use App\Services\ProductAggregationService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $productService;

    public function __construct(ProductAggregationService $productService)
    {
        $this->productService = $productService;
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);
        $productId = $request->product_id;

        $cart[$productId] = isset($cart[$productId])
            ? $cart[$productId] + (int) $request->quantity
            : (int) $request->quantity;

        session(['cart' => $cart]);

        return redirect()->route('products.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $quantity) {
            try {
                $product = $this->productService->getProductDetail($productId);
            } catch (\Exception $e) {
                continue; 
            }

            $itemSubtotal = $product['price'] * $quantity;
            $subtotal += $itemSubtotal;

            $items[] = [
                'product_id' => $productId,
                'title' => $product['title'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $itemSubtotal,
                'image' => $product['image'] ?? null,
            ];
        }

        return view('cart.index', compact('items', 'subtotal'));
    }

    public function remove($productId)
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}