<?php

namespace App\Http\Controllers;

use App\Services\ProductAggregationService;

class ProductWebController extends Controller
{
    protected $productService;

    public function __construct(ProductAggregationService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();

        return view('products.index', compact('products'));
    }
}