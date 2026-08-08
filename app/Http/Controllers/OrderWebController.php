<?php

namespace App\Http\Controllers;

use App\Order;
use App\Services\OrderService;
use App\Services\ProductAggregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderWebController extends Controller
{
    protected $orderService;
    protected $productService;

    public function __construct(OrderService $orderService, ProductAggregationService $productService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
    }

    public function create(Request $request)
    {
        $productId = $request->query('product_id');
        $product = $productId ? $this->productService->getProductDetail($productId) : null;

        return view('orders.create', compact('product'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'destination' => 'required|string',
            'courier' => 'required|in:jne,jnt,sicepat',
        ]);

        try {
            $order = $this->orderService->createOrder([
                'idempotency_key' => (string) Str::uuid(),
                'user_id' => auth()->id(),
                'items' => [
                    ['product_id' => $request->product_id, 'quantity' => $request->quantity],
                ],
                'shipping' => [
                    'destination' => $request->destination,
                    'courier' => $request->courier,
                ],
            ]);

            return redirect()
                ->route('orders.show', $order->order_number)
                ->with('success', 'Order berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items', 'payment', 'shipment'])
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function index()
    {
        $orders = Order::with(['items', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function createFromCart()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Keranjang kosong.']);
        }

        return view('orders.checkout-cart');
    }

    public function storeFromCart(Request $request)
    {
        $request->validate([
            'destination' => 'required|string',
            'courier' => 'required|in:jne,jnt,sicepat',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Keranjang kosong.']);
        }

        $items = [];
        foreach ($cart as $productId => $quantity) {
            $items[] = ['product_id' => $productId, 'quantity' => $quantity];
        }

        try {
            $order = $this->orderService->createOrder([
                'idempotency_key' => (string) Str::uuid(),
                'items' => $items,
                'shipping' => [
                    'destination' => $request->destination,
                    'courier' => $request->courier,
                ],
            ]);

            session()->forget('cart'); 

            return redirect()
                ->route('orders.show', $order->order_number)
                ->with('success', 'Order berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function updateStatus(Request $request, $orderNumber)
    {
        $request->validate([
            'status' => 'required|in:PACKING,IN_WAREHOUSE,ON_DELIVERY,DELIVERED',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        try {
            $this->orderService->transitionStatus($order, $request->status);
            return back()->with('success', 'Status order berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}