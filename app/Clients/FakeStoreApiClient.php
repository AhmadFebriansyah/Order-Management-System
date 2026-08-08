<?php

namespace App\Clients;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FakeStoreApiClient
{
    protected $client;
    protected $baseUrl = 'https://fakestoreapi.com';

    public function __construct()
    {
        $this->client = new Client([
        'timeout' => 5,
        'verify' => env('APP_ENV') === 'local' ? false : true, 
    ]);
    }

    public function getAllProducts()
    {
        $start = microtime(true);

        $response = $this->client->get("{$this->baseUrl}/products");
        $data = json_decode($response->getBody(), true);

        Log::info('External API call: FakeStoreAPI getAllProducts', [
            'duration_ms' => round((microtime(true) - $start) * 1000),
            'status' => $response->getStatusCode(),
        ]);

        return $data;
    }

    public function getProductById($id)
    {
        $start = microtime(true);

        $response = $this->client->get("{$this->baseUrl}/products/{$id}");
        $data = json_decode($response->getBody(), true);

        Log::info('External API call: FakeStoreAPI getProductById', [
            'product_id' => $id,
            'duration_ms' => round((microtime(true) - $start) * 1000),
            'status' => $response->getStatusCode(),
        ]);

        return $data;
    }
}