<?php

namespace App\Http\Controllers\api\v1\psa;

use App\Http\Controllers\Controller;
use App\Models\psa\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlightController extends Controller
{
    public function index()
    {
        try {
            // 1. Test Connection specifically for PSA
            $pdo = DB::connection('mongodb_psa')->getPdo();
            
            // 2. Fetch Flights
               // Order by Origin first (A-Z), then by Destination (A-Z)
            $flights = Flight::orderBy('origin', 'asc')
                 ->orderBy('destination', 'asc')
                 ->get();

            // 3. Return Data
            return response()->json($flights);

        } catch (\Exception $e) {
            // 🔴 THIS CATCHES THE 500 ERROR AND SHOWS IT 🔴
            return response()->json([
                'status' => 'error',
                'message' => 'Server Crash Detected',
                'technical_detail' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}