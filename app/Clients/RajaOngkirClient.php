<?php

namespace App\Clients;

use Illuminate\Support\Facades\Log;

class RajaOngkirClient
{
    public function calculateCost($destination, $courier, $weight = 1000)
    {
        $start = microtime(true);

        // Simulasi tarif per kurir (dalam rupiah)
        $rates = [
            'jne' => 1.00,
            'jnt' => 1.20,
            'sicepat' => 1.30,
        ];

        $cost = $rates[$courier];

        Log::info('External API call (simulated): RajaOngkir calculateCost', [
            'destination' => $destination,
            'courier' => $courier,
            'duration_ms' => round((microtime(true) - $start) * 1000),
        ]);

        return [
            'courier' => $courier,
            'service' => 'REG',
            'cost' => $cost,
            'etd' => '2-3 hari',
        ];
    }
}