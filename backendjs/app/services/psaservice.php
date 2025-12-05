<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PsaService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('PSA_SERVICE_URL');
    }

    // Example: Get all flights
    public function getFlights()
    {
        // Tatawagin nito ang: http://localhost:8000/api/psa/flights
        $response = Http::get("{$this->baseUrl}/psa/flights");

        if ($response->successful()) {
            return $response->json();
        }

        return null; // O kaya mag throw ng error
    }

    // Example: Search Flight
    public function searchFlights($params)
    {
        // Ipapasa ang query parameters
        $response = Http::get("{$this->baseUrl}/psa/flights/search", $params);
        return $response->json();
    }
}