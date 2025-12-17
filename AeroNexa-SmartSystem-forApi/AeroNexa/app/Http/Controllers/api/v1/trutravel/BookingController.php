<?php

namespace App\Http\Controllers\api\v1\trutravel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

// MODELS
use App\Models\trutravel\Booking as TruBooking;
use App\Models\trutravel\Package;
use App\Models\psa\Booking as PsaBooking;
use App\Models\aureliya\Booking as AurBooking;
use App\Models\skyroute\Booking as SkyBooking;
use App\Models\skyroute\Location as SkyLocation;
use App\Models\skyroute\Vehicle as SkyVehicle;

// TRAIT
use App\Traits\HandlesAeroPay; 

class BookingController extends Controller
{
    use HandlesAeroPay; 

    public function store(Request $req)
{
    // A. VALIDATION
    if (!$req->package_id || !$req->travel_date) {
        return response()->json(['error' => 'Missing Package ID or Travel Date'], 400);
    }

    // 1. Get Guest Count (Default 2)
    $guests = (int) ($req->guests ?? 2);
    if ($guests < 1) $guests = 1;

    $pickupTime = $req->travel_time ?? '08:00';

    $package = Package::find($req->package_id);
    if (!$package) return response()->json(['error' => 'Package not found'], 404);

    // B. CALCULATE DATES
    $startDate = Carbon::parse($req->travel_date);
    $endDate = $startDate->copy()->addDays((int) $package->nights);
    $travelDateStr = $startDate->format('Y-m-d');
    $returnDateStr = $endDate->format('Y-m-d');

    // C. FIND VEHICLE (Strict Mode)
    $skyVeh = SkyVehicle::find($package->skyroute_vehicle_id);
    if (!$skyVeh) {
        $skyVeh = SkyVehicle::where('_id', $package->skyroute_vehicle_id)->first();
    }
    if (!$skyVeh) {
        return response()->json([
            'error' => 'Data Integrity Error: The Vehicle linked to this package does not exist in SkyRoute.',
            'missing_vehicle_id' => $package->skyroute_vehicle_id
        ], 404);
    }

    // 2. CALCULATE TOTAL PRICE (Price x Guests)
    $totalAmount = $package->final_price * $guests;

    // D. PREPARE IDs
    $userId = (string) (TruBooking::count() + 1);
    $bookingUuid = (string) Str::uuid(); 

    $metadata = [
        "package_name"   => $package->name,
        "payment_method" => "AeroPay",
        "guests"         => $guests,
        "travel_date"    => $travelDateStr,
        "pickup_time"    => $pickupTime, // <--- Added to AeroPay receipt
        "return_date"    => $returnDateStr
    ];

    // E. PROCESS PAYMENT (For the TOTAL amount)
    $paymentResponse = $this->createAeroPayPayment(
        $userId,
        $totalAmount,    // <--- Paying for everyone
        $bookingUuid,   
        'TRUTRAVEL',    
        $metadata,      
        'confirmed'     
    );

    if (!$paymentResponse['success']) {
        return response()->json(['error' => 'Payment Error: ' . $paymentResponse['message']], 500);
    }

    $trxCode = $paymentResponse['transaction_code'];

    // F. SEED PSA (FLIGHTS)
    // Note: Ideally you create 1 row per passenger, but for this simple system, 
    // we just book the main user twice (Outbound/Return).
    $psaOut = PsaBooking::create([
        '_id' => (string) Str::uuid(),
        'user_id' => $userId,
        'flight_id' => $package->airline_flight_id, 
        'flight_date' => $travelDateStr,
        'status' => 'confirmed',
        'transaction_code' => $trxCode,
        'booking_date' => now(),
        'payment_method' => 'TRUTRAVEL', 
        'total_price' => 0 
    ]);

    $psaRet = PsaBooking::create([
        '_id' => (string) Str::uuid(),
        'user_id' => $userId,
        'flight_id' => $package->airline_return_flight_id, 
        'flight_date' => $returnDateStr,
        'status' => 'confirmed',
        'transaction_code' => $trxCode, 
        'booking_date' => now(),
        'payment_method' => 'TRUTRAVEL',
        'total_price' => 0 
    ]);

    // G. SEED AURELIYA (HOTEL) - Updates Guest Count
    $aur = AurBooking::create([
        '_id' => (string) Str::uuid(),
        'user_id' => $userId,
        'property_id' => $package->aureliya_property_id,
        'check_in' => $travelDateStr,
        'check_out' => $returnDateStr,
        'guests' => $guests, // <--- Correct Guest Count
        'total_price' => $totalAmount, // Full package price stored here or split as needed
        'status' => 'confirmed',
        'payment_status' => 'paid', 
        'transaction_code' => $trxCode,
        'booking_date' => now(),
        'payment_method' => 'TRUTRAVEL'
    ]);

    // H. SEED SKYROUTE (TRANSPORT) - Updates Passenger Count
    $skyOrigin = SkyLocation::find($package->skyroute_origin_id);
    $skyDest = SkyLocation::find($package->skyroute_destination_id);
    
    $sky = SkyBooking::create([
        '_id' => (string) Str::uuid(),
        'user_id' => $userId,
        'transaction_code' => $trxCode,
        'origin_location_id' => $package->skyroute_origin_id,
        'destination_location_id' => $package->skyroute_destination_id,
        'vehicle_id' => $package->skyroute_vehicle_id,
        'vehicle_plate' => $skyVeh->plate_number, 
        'vehicle_name'  => $skyVeh->name, 
        'vehicle_type'  => $skyVeh->type ?? 'Package Vehicle',
        'origin_city' => $skyOrigin->city ?? 'Unknown',
        'destination_city' => $skyDest->city ?? 'Unknown',
        
        'date' => $travelDateStr,
        'time' => $pickupTime, // <--- SAVED HERE
        
        'passengers' => $guests, 
        'total_price' => $totalAmount, 
        'status' => 'confirmed',
        'payment_status' => 'confirmed', 
        'created_at' => now(),
        'payment_method' => 'TRUTRAVEL'
    ]);

    // I. SAVE TRUTRAVEL BOOKING
    $booking = TruBooking::create([
        '_id' => $bookingUuid,
        'user_id' => $userId,
        'package_id' => $package->_id,
        'travel_date' => $travelDateStr,
        'return_date' => $returnDateStr,
        'amount' => $totalAmount, // <--- Save Total Price
        'transaction_code' => $trxCode,
        'status' => 'confirmed',
        'payment_status' => 'paid', 
        'created_at' => now(),
        'payment_breakdown' => json_encode([
            'psa_outbound' => $psaOut->_id,
            'psa_return' => $psaRet->_id,
            'aureliya' => $aur->id ?? $aur->_id,
            'skyroute' => $sky->id ?? $sky->_id
        ])
    ]);

    return response()->json([
        'message' => 'Package Booked Successfully!',
        'user_id' => $userId,
        'package' => $package->name,
        'transaction_code' => $trxCode,
        'vehicle_assigned' => $skyVeh->name . ' (' . $skyVeh->plate_number . ')',
        'total_price' => $totalAmount,
        'dates' => "$travelDateStr to $returnDateStr"
    ]);
}
}