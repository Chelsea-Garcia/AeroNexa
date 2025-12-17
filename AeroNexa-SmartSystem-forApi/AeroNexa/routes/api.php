<?php

use Illuminate\Support\Facades\Route;
use App\Models\trutravel\Package;
use App\Models\skyroute\Vehicle;

// --- 1. PSA (FLIGHTS) ---
// List Flights
Route::get('/psa-flights', [\App\Http\Controllers\api\v1\psa\FlightController::class, 'index']);
// Book Flight
Route::post('/psa-book', [\App\Http\Controllers\api\v1\psa\BookingController::class, 'store']);


// --- 2. AURELIYA (HOTELS) ---
// List Properties
Route::get('/aur-props', [\App\Http\Controllers\api\v1\aureliya\PropertyController::class, 'index']);
// Book Hotel
Route::post('/aur-book', [\App\Http\Controllers\api\v1\aureliya\BookingController::class, 'store']);


// --- 3. SKYROUTE (TRANSPORT) ---

// 🔴 THE MISSING PART: LOCATION DROPDOWNS
// Calculate Price (Real-time)
Route::post('/sky-calculate', [\App\Http\Controllers\api\v1\skyroute\BookingController::class, 'calculate']);
Route::get('/sky-loc/countries', [\App\Http\Controllers\api\v1\skyroute\LocationController::class, 'getCountries']);
Route::get('/sky-loc/divisions/{country}', [\App\Http\Controllers\api\v1\skyroute\LocationController::class, 'getDivisions']);
Route::get('/sky-loc/cities/{div}', [\App\Http\Controllers\api\v1\skyroute\LocationController::class, 'getCities']);

// Book Ride
Route::post('/sky-book', [\App\Http\Controllers\api\v1\skyroute\BookingController::class, 'store']);

// --- TRUTRAVEL ROUTES ---
Route::get('/tru-packages', [\App\Http\Controllers\api\v1\trutravel\PackageController::class, 'index']);
Route::post('/tru-book', [\App\Http\Controllers\api\v1\trutravel\BookingController::class, 'store']);

// --- 5. AEROPAY (TRANSACTION) ---
Route::post('/aeropay/charge', [\App\Http\Controllers\api\v1\aeropay\TransactionController::class, 'charge']);
Route::get('/aeropay/transactions', [\App\Http\Controllers\api\v1\aeropay\TransactionController::class, 'index']);
Route::put('aeropay/transactions/{transactionCode}/status', [\App\Http\Controllers\api\v1\aeropay\TransactionController::class, 'updateStatus']);

// routes/api.php

Route::get('/debug/fix-packages', function () {
    // 1. Get ALL Real Vehicles from SkyRoute (MongoDB)
    $realVehicles = Vehicle::all();
    
    if ($realVehicles->isEmpty()) {
        return "❌ Error: You have NO vehicles in SkyRoute. Create some vehicles first!";
    }

    // 2. Get ALL Packages (MySQL)
    $packages = Package::all();
    $updatedCount = 0;

    foreach ($packages as $pkg) {
        // Pick a random REAL vehicle
        $randomVehicle = $realVehicles->random();

        // Overwrite the invalid ID with the Real ID
        $pkg->skyroute_vehicle_id = $randomVehicle->_id;
        $pkg->save();

        $updatedCount++;
    }

    return [
        "message" => "✅ FIXED!",
        "packages_updated" => $updatedCount,
        "new_vehicle_assigned" => "All packages now point to valid SkyRoute vehicles."
    ];
});