<?php

namespace App\Services;

use App\Clients\RajaOngkirClient;

class ShippingService
{
    protected $client;

    public function __construct(RajaOngkirClient $client)
    {
        $this->client = $client;
    }

    public function calculate($destination, $courier)
    {
        return $this->client->calculateCost($destination, $courier);
    }
}