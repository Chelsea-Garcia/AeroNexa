<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PsaService; // Import mo yung service

class PublicFlightController extends Controller
{
    protected $psaService;

    // Dependency Injection
    public function __construct(PsaService $psaService)
    {
        $this->psaService = $psaService;
    }

    public function index()
    {
        // Kukunin ang data mula sa PSA Service (localhost:8000)
        $flights = $this->psaService->getFlights();

        // Ibalik sa Frontend niyo
        return response()->json([
            'source' => 'PSA Microservice',
            'data' => $flights
        ]);
    }
}