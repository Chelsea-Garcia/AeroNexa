<?php

namespace App\Http\Controllers\api\v1\skyroute;

use App\Http\Controllers\Controller;
use App\Models\skyroute\Booking;
use App\Models\skyroute\Vehicle;
use App\Models\skyroute\Location;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * STORE BOOKING (Auto-Assign Vehicle Logic)
     */
    public function store(Request $request)
    {
        // 1. Validate Inputs
        $validated = $request->validate([
            'user_id' => 'required',
            'origin' => 'required',        // Location ID
            'destination' => 'required',   // Location ID
            'date' => 'required|date',
            'time' => 'required',
            'passengers' => 'required|integer|min:1',
            'vehicle_type' => 'required|string' // 'Bus', 'Car', 'SUV'
        ]);

        // 2. HANAPIN ANG SASAKYAN (Auto-Assign Logic)
        // Kukuha ng sasakyan na:
        // - TAMA ang Type (e.g., Bus)
        // - KASYA ang Passengers (Capacity >= Request)
        $vehicle = Vehicle::where('type', $validated['vehicle_type'])
                          ->where('capacity', '>=', (int)$validated['passengers'])
                          ->first(); // Pwede mong gawing ->inRandomOrder()->first() kung gusto mo random

        // Kung walang mahanap na sasakyan
        if (!$vehicle) {
            return response()->json([
                'message' => "No available {$validated['vehicle_type']} fits {$validated['passengers']} passengers."
            ], 404);
        }

        // 3. Create Booking
        try {
            $booking = Booking::create([
                'user_id' => $validated['user_id'],
                'vehicle_id' => $vehicle->id, // Dito na-assign ang sasakyan
                'vehicle_name' => $vehicle->name, // Save name for easier display
                'vehicle_plate' => $vehicle->plate_number,
                'origin_location_id' => $validated['origin'],
                'destination_location_id' => $validated['destination'],
                'date' => $validated['date'],
                'time' => $validated['time'],
                'passengers' => $validated['passengers'],
                'status' => 'confirmed',
                'created_at' => now()
            ]);

            return response()->json([
                'message' => 'Booking Successful',
                'vehicle_assigned' => $vehicle->name,
                'plate_number' => $vehicle->plate_number,
                'booking_id' => $booking->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Booking Failed: ' . $e->getMessage()], 500);
        }
    }
}