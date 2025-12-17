<?php

namespace App\Http\Controllers\api\v1\psa;

use App\Http\Controllers\Controller;
use App\Models\psa\Booking;
use App\Models\psa\Flight;
use App\Models\psa\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    // --- 1. LIST FLIGHTS ---
    public function index()
    {
        return response()->json(Flight::all());
    }

    // --- 2. CREATE BOOKING ---
    public function store(Request $req)
    {
        // A. VALIDATION (Only validate what the User Inputs)
        $validator = Validator::make($req->all(), [
            'flight_id' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'contact_number' => 'required|string',
            'gender' => 'required|string',
            'birthdate' => 'required|date',
            'nationality' => 'required|string',
            'passport_number' => 'required|string',
            'passport_expiry' => 'required|date',
            // Optional fields
            'special_assistance' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // B. FIND FLIGHT
        // Force MongoDB string lookup
        $flight = Flight::where('_id', $req->flight_id)->first();

        // Debugging: If it fails, tell us exactly what ID we looked for
        if (!$flight) {
            return response()->json([
                'error' => 'Flight not found',
                'received_id' => $req->flight_id // This helps you see if the ID is empty!
            ], 404);
        }

        // C. CREATE PASSENGER (The ID comes from here!)
        $passenger = Passenger::create([
            '_id' => (string) Str::uuid(),
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'email' => $req->email,
            'contact_number' => $req->contact_number,
            'gender' => $req->gender,
            'birthdate' => $req->birthdate,
            'nationality' => $req->nationality,
            'passport_number' => $req->passport_number,
            'passport_expiry' => $req->passport_expiry,
            'special_assistance' => $req->special_assistance,
            'emergency_contact_name' => $req->emergency_contact_name,
            'emergency_contact_number' => $req->emergency_contact_number,
        ]);

        // D. CREATE BOOKING (Auto-fill the missing data)
        $amount = $flight->price ?? 2500.00;
        $trxCode = 'APAY-' . strtoupper(Str::random(10));
        
        // Auto-generate User ID (or use a placeholder)
        $nextId = Booking::count() + 1;
        $newUserId = $nextId;

        // Get Date from Flight (Database) instead of User Input
        $flightDate = $flight->departure_time ?? now();

        $booking = Booking::create([
            '_id' => (string) Str::uuid(),
            'user_id' => $newUserId,          // Auto-generated
            'passenger_id' => $passenger->_id, // From the new passenger above
            'flight_id' => $flight->_id,
            'flight_date' => $flightDate,      // From Flight DB
            'status' => 'confirmed',
            'transaction_code' => $trxCode,
            'booking_date' => now(),
        ]);

        // E. SEED AEROPAY
        try {
            DB::connection('aeropay')->table('transactions')->insert([
                '_id' => (string) Str::uuid(),
                'transaction_code' => $trxCode,
                'user_id' => $newUserId,
                'partner' => 'PSA',
                'partner_reference_id' => $booking->_id,
                'amount' => $amount,
                'currency' => 'PHP',
                'status' => 'pending',
                'metadata' => json_encode([
                    'flight' => $flight->flight_number ?? 'Unknown Flight',
                    'route' => ($flight->origin ?? '?') . ' -> ' . ($flight->destination ?? '?'),
                    'passenger' => $passenger->first_name . ' ' . $passenger->last_name
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) { /* Ignore errors for seeding */ }

        return response()->json([
            'message' => 'Booking Successful',
            'booking_id' => $booking->_id,
            'passenger_id' => $passenger->_id, // Proof it worked
            'auto_user_id' => $newUserId
        ], 201);
    }
}   