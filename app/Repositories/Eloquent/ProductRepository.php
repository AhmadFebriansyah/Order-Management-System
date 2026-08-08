<?php

namespace App\Repositories\Eloquent;

use App\Clients\FakeStoreApiClient;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductRepository implements ProductRepositoryInterface
{
    protected $apiClient;

    public function __construct(FakeStoreApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function all()
    {
        return Cache::remember('products:all', 300, function () {
            try {
                return $this->apiClient->getAllProducts();
            } catch (\Exception $e) {
                Log::error('Failed to fetch products from external API', [
                    'error' => $e->getMessage(),
                ]);
                return Cache::get('products:all:fallback', []);
            }
        });
    }

    public function findById($externalProductId)
    {
        $cacheKey = "products:{$externalProductId}";

        return Cache::remember($cacheKey, 300, function () use ($externalProductId) {
            try {
                $product = $this->apiClient->getProductById($externalProductId);
                Cache::put("products:{$externalProductId}:fallback", $product, 60 * 24);
                return $product;
            } catch (\Exception $e) {
                Log::error('Failed to fetch product from external API', [
                    'product_id' => $externalProductId,
                    'error' => $e->getMessage(),
                ]);
                return Cache::get("products:{$externalProductId}:fallback");
            }
        });
    }
}