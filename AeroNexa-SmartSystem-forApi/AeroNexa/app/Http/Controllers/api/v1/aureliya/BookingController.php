<?php

namespace App\Http\Controllers\api\v1\aureliya;

use App\Http\Controllers\Controller;
use App\Models\aureliya\Booking;
use App\Models\aureliya\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Traits\HandlesAeroPay; 

class BookingController extends Controller
{
    use HandlesAeroPay;

    public function store(Request $req)
    {
        // --- 1. VALIDATION ---
        $validator = Validator::make($req->all(), [
            'property_id' => 'required', 
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // --- 2. FIND PROPERTY ---
        $property = Property::where('_id', $req->property_id)->first();
        
        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // --- 3. CALCULATE PRICE ---
        $in = new \DateTime($req->check_in);
        $out = new \DateTime($req->check_out);
        $nights = $out->diff($in)->days;
        if ($nights < 1) $nights = 1;
        
        $totalPrice = $nights * ($property->price_per_night ?? 2000);

        // --- 4. PREPARE DATA ---
        $nextId = Booking::count() + 1;
        $userId = (string) $nextId; 
        
        // 🔴 FIX: Generate UUID for the AeroPay Reference (Not User ID)
        $bookingUuid = (string) Str::uuid(); 

        // 🔴 FIX: Metadata Format
        // Note: Using 'title' or 'name' depending on what your DB has
        $propName = $property->title ?? $property->name ?? 'Unknown Property';

        $metadata = [
            "property_name"  => $propName,
            "payment_method" => "AeroPay",
            "check_in"       => $req->check_in,
            "check_out"      => $req->check_out
        ];

        // --- 5. PROCESS AEROPAY TRANSACTION ---
        // We pass 'confirmed' here so the AeroPay transaction status is confirmed immediately
        $paymentResponse = $this->createAeroPayPayment(
            $userId,            
            $totalPrice,        
            $bookingUuid,       // <--- Passing the UUID
            'AURELIYA',         
            $metadata,          // <--- Passing the Metadata
            'confirmed'         // <--- Force Status
        );

        if (!$paymentResponse['success']) {
            return response()->json([
                'message' => 'Booking Failed: Payment System Error',
                'details' => $paymentResponse['message']
            ], 500);
        }

        $transactionCode = $paymentResponse['transaction_code'];

        // --- 6. SAVE BOOKING ---
        $booking = Booking::create([
            '_id' => $bookingUuid, // Use the same UUID for the Booking ID
            'user_id' => $userId,
            'property_id' => $req->property_id, 
            'check_in' => $req->check_in,
            'check_out' => $req->check_out,
            'guests' => $req->guests,
            'total_price' => $totalPrice,
            
            'status' => 'confirmed', 
            
            // 🔴 FIX for 1265 Error: Using 'paid' is safer for ENUM columns. 
            // If you REALLY need 'confirmed' here, you must run the SQL below.
            'payment_status' => 'paid', 
            
            'transaction_code' => $transactionCode,
            'booking_date' => now(),
            'payment_method' => 'AEROPAY', 
        ]);

        return response()->json([
            'message' => 'Booking Successful',
            'booking_id' => $booking->_id,
            'transaction_code' => $transactionCode,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'total_price' => $totalPrice,
            'property_address' => $property->address ?? $property->city
        ], 201);
    }
}