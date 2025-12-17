<?php

namespace App\Http\Controllers\api\v1\skyroute;

use App\Http\Controllers\Controller;
use App\Models\skyroute\Booking;
use App\Models\skyroute\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\HandlesAeroPay; // <--- Import Trait

class BookingController extends Controller
{
    use HandlesAeroPay; // <--- Use Trait

    // --- REAL-TIME CALCULATOR (Unchanged) ---
    public function calculate(Request $req)
    {
        $origin = Location::where('city', $req->org_city)->first();
        $dest = Location::where('city', $req->dst_city)->first();

        if (!$origin || !$dest) {
            return response()->json(['distance' => 0, 'price' => 0]);
        }

        $earthRadius = 6371; 
        $dLat = deg2rad($dest->latitude - $origin->latitude);
        $dLon = deg2rad($dest->longitude - $origin->longitude);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($origin->latitude)) * cos(deg2rad($dest->latitude)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distanceKm = $earthRadius * $c;

        $basePrice = match($req->vehicle_type) { 'Bus' => 50, 'SUV' => 200, 'Car' => 100, default => 100 };
        $totalPrice = $basePrice + ($req->passengers * 20) + ($distanceKm * 5);

        return response()->json([
            'distance' => round($distanceKm, 2),
            'price' => round($totalPrice, 2)
        ]);
    }

    // --- BOOKING STORE (Updated for AeroPay) ---
    public function store(Request $req)
    {
        // 1. GET LOCATIONS
        $origin = Location::where('city', $req->org_city)->first();
        $dest = Location::where('city', $req->dst_city)->first();

        if (!$origin || !$dest) {
            return response()->json(['error' => 'Invalid Locations selected.'], 404);
        }

        // 2. FIND AVAILABLE VEHICLE
        $vehicles = \App\Models\skyroute\Vehicle::where('location_id', $origin->_id)
                    ->where('type', $req->vehicle_type)
                    ->get();

        if ($vehicles->count() > 0) {
            $vehicle = $vehicles->random(); 
        } else {
            // Fallback Simulation
            $vehicle = (object) [
                '_id' => new \MongoDB\BSON\ObjectId(),
                'name' => 'Standard ' . $req->vehicle_type,
                'plate_number' => 'TMP-' . rand(100, 999)
            ];
        }

        // 3. CALCULATE PRICE & DISTANCE
        $earthRadius = 6371; 
        $dLat = deg2rad($dest->latitude - $origin->latitude);
        $dLon = deg2rad($dest->longitude - $origin->longitude);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($origin->latitude)) * cos(deg2rad($dest->latitude)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distanceKm = $earthRadius * $c;

        $basePrice = match($req->vehicle_type) { 'Bus' => 50, 'SUV' => 200, 'Car' => 100, default => 100 };
        $maxPax = match($req->vehicle_type) { 'Bus' => 56, 'SUV' => 7, 'Car' => 5, default => 4 };

        if ($req->passengers > $maxPax) {
            return response()->json(['error' => "Max capacity for {$req->vehicle_type} is {$maxPax}."], 422);
        }

        $totalPrice = round($basePrice + ($req->passengers * 20) + ($distanceKm * 5), 2);

        // 4. PREPARE AEROPAY DATA
        $nextId = Booking::count() + 1;
        $userId = (string) $nextId;
        
        // Generate UUID for Reference
        $bookingUuid = (string) Str::uuid();

        // Prepare Metadata
        $metadata = [
            "origin"         => $req->org_city,
            "destination"    => $req->dst_city,
            "vehicle_type"   => $req->vehicle_type,
            "vehicle_plate"  => $vehicle->plate_number,
            "travel_date"    => $req->date . ' ' . $req->time,
            "payment_method" => "AeroPay"
        ];

        // 5. PROCESS AEROPAY TRANSACTION
        $paymentResponse = $this->createAeroPayPayment(
            $userId,
            $totalPrice,
            $bookingUuid,   // Reference ID
            'SKYROUTE',     // Partner Name
            $metadata,      // Metadata
            'confirmed'     // Status
        );

        if (!$paymentResponse['success']) {
            return response()->json([
                'message' => 'Booking Failed: Payment System Error',
                'details' => $paymentResponse['message']
            ], 500);
        }

        $trxCode = $paymentResponse['transaction_code'];

        // 6. SAVE BOOKING (MongoDB)
        $booking = Booking::create([
            '_id' => $bookingUuid, // Use UUID as Mongo ID
            'user_id' => $userId,
            'transaction_code' => $trxCode,
            'origin_location_id' => $origin->_id,
            'destination_location_id' => $dest->_id,
            'origin_city' => $req->org_city,
            'destination_city' => $req->dst_city,
            'vehicle_id' => $vehicle->_id,
            'vehicle_name' => $vehicle->name ?? 'Standard',
            'vehicle_plate' => $vehicle->plate_number,
            'vehicle_type' => $req->vehicle_type,
            'date' => $req->date,
            'time' => $req->time,
            'passengers' => $req->passengers,
            'distance_km' => round($distanceKm, 2),
            'total_price' => $totalPrice,
            
            'status' => 'confirmed',
            'payment_status' => 'confirmed', // MongoDB allows this string easily
            
            'created_at' => now()
        ]);

        return response()->json([
            'message' => 'Ride Booked Successfully!',
            'user_id' => $userId,
            'vehicle' => $vehicle->name ?? $req->vehicle_type,
            'plate_number' => $vehicle->plate_number,
            'price' => $totalPrice,
            'distance' => round($distanceKm, 2) . ' km',
            'transaction_code' => $trxCode
        ]);
    }
}