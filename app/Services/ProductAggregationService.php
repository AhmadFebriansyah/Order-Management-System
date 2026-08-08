<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductAggregationService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductDetail($externalProductId)
    {
        $product = $this->productRepository->findById($externalProductId);

        if (!$product) {
            throw new \Exception("Product {$externalProductId} not found or unavailable");
        }

        return $product;
    }
}