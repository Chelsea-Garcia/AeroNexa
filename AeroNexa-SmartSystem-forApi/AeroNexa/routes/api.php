<?php

use Illuminate\Support\Facades\Route;

// --- CONTROLLERS ---
use App\Http\Controllers\api\v1\psa\FlightController;
use App\Http\Controllers\api\v1\psa\PassengerController;
use App\Http\Controllers\api\v1\psa\AirportController;
use App\Http\Controllers\api\v1\psa\BookingController as PsaBookingController;
use App\Http\Controllers\api\v1\aeropay\TransactionController;
use App\Http\Controllers\api\v1\aureliya\PropertyController;
use App\Http\Controllers\api\v1\aureliya\AmenityController;
use App\Http\Controllers\api\v1\aureliya\ReviewController;
use App\Http\Controllers\api\v1\aureliya\BookingController as AureliyaBookingController;
use App\Http\Controllers\api\v1\skyroute\LocationController;
use App\Http\Controllers\api\v1\skyroute\VehicleController;
use App\Http\Controllers\api\v1\skyroute\BookingController as SkyrouteBookingController;
use App\Http\Controllers\api\v1\trutravel\PackageController;
use App\Http\Controllers\api\v1\trutravel\BookingController as TrutravelBookingController;
use App\Http\Controllers\api\v1\AeroNexa\UserController;

/*
|--------------------------------------------------------------------------
| 1. ORIGINAL API ROUTES (Don't delete these!)
|--------------------------------------------------------------------------
*/

// --- PSA (Flights) ---
Route::prefix('psa')->group(function () {
    Route::get('/flights', [FlightController::class, 'index']);
    Route::get('/flights/{id}', [FlightController::class, 'show']);
    Route::get('/flights/search', [FlightController::class, 'search']);
    Route::post('/passengers', [PassengerController::class, 'store']);
    Route::get('/passengers/user/{user_id}', [PassengerController::class, 'showByUser']);
    Route::put('/passengers/{id}', [PassengerController::class, 'update']);
    Route::get('/airports', [AirportController::class, 'index']);
    Route::get('/airports/{id}', [AirportController::class, 'show']);
    Route::post('/bookings', [PsaBookingController::class, 'store']);
    Route::get('/bookings/{user_id}', [PsaBookingController::class, 'userBookings']);
    Route::get('/booking/{id}', [PsaBookingController::class, 'show']);
    Route::put('/booking/{id}/passenger', [PsaBookingController::class, 'updatePassenger']);
    Route::post('/booking/{id}/cancel', [PsaBookingController::class, 'cancel']);
    Route::put('/booking/{id}/status', [PsaBookingController::class, 'updateStatus']);
});

// --- AEROPAY (Payments) ---
Route::prefix('aeropay')->group(function () {
    Route::post('/charge', [TransactionController::class, 'charge']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::get('/transactions/user/{user_id}', [TransactionController::class, 'userTransactions']);
    Route::get('/transactions/status/{status}', [TransactionController::class, 'filterByStatus']);
    Route::put('/transactions/{id}/status', [TransactionController::class, 'updateStatus']);
    Route::post('/webhook', [TransactionController::class, 'webhook']);
});

// --- AURELIYA (Hotels) ---
Route::prefix('aureliya')->group(function () {
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::get('/amenities', [AmenityController::class, 'index']);
    Route::get('/amenities/{id}', [AmenityController::class, 'show']);
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/{id}', [ReviewController::class, 'show']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::get('/bookings', [AureliyaBookingController::class, 'index']);
    Route::get('/bookings/{id}', [AureliyaBookingController::class, 'show']);
    Route::post('/bookings', [AureliyaBookingController::class, 'store']);
    Route::put('/bookings/{id}', [AureliyaBookingController::class, 'update']);
    Route::put('/booking/{id}/status', [AureliyaBookingController::class, 'updateStatus']);
});

// --- SKYROUTE (Transport) ---
Route::prefix('skyroute')->group(function () {
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{id}', [LocationController::class, 'show']);
    Route::get('/vehicles/city/{city}', [VehicleController::class, 'vehiclesByCity']);
    Route::post('/bookings', [SkyrouteBookingController::class, 'store']);
    Route::get('/bookings/user/{user_id}', [SkyrouteBookingController::class, 'userBookings']);
    Route::get('/booking/{id}', [SkyrouteBookingController::class, 'show']);
    Route::post('/booking/{id}/cancel', [SkyrouteBookingController::class, 'cancel']);
    Route::put('/booking/{id}/status', [SkyrouteBookingController::class, 'updateStatus']);
});

// --- TRUTRAVEL (Packages) ---
Route::prefix('trutravel')->group(function () {
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/packages/{id}', [PackageController::class, 'show']);
    Route::post('/bookings', [TrutravelBookingController::class, 'store']);
    Route::get('/bookings/user/{user_id}', [TrutravelBookingController::class, 'userBookings']);
    Route::get('/booking/{id}', [TrutravelBookingController::class, 'show']);
    Route::post('/booking/{id}/cancel', [TrutravelBookingController::class, 'cancel']);
    Route::put('/booking/{id}/status', [TrutravelBookingController::class, 'updateStatus']);
    Route::post('/webhook', [TrutravelBookingController::class, 'webhook']);
});

// --- AERONEXA (Users) ---
Route::prefix('aeronexa')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::post('users/login', [UserController::class, 'login']);
});


/*
|--------------------------------------------------------------------------
| 2. DASHBOARD BRIDGE ROUTES (For admin.php)
|--------------------------------------------------------------------------
| These are the shortcuts your Admin Panel needs.
*/

// PSA (Flights)
Route::get('/psa-flights', [FlightController::class, 'index']);
Route::post('/psa-book', [PsaBookingController::class, 'store']);

// Aureliya (Hotels)
Route::get('/aur-props', [PropertyController::class, 'index']);
Route::post('/aur-book', [AureliyaBookingController::class, 'store']);

// SkyRoute (Transport)
Route::get('/sky-loc/countries', [LocationController::class, 'getCountries']);
Route::get('/sky-loc/divisions/{country}', [LocationController::class, 'getDivisions']);
Route::get('/sky-loc/cities/{division}', [LocationController::class, 'getCities']);
Route::post('/sky-calculate', [SkyrouteBookingController::class, 'calculate']); 
Route::post('/sky-book', [SkyrouteBookingController::class, 'store']);

// TruTravel (Packages)
Route::get('/tru-packages', [PackageController::class, 'index']);
Route::post('/tru-book', [TrutravelBookingController::class, 'store']);

// AeroPay (Transaction Status Update)
// Note: This matches the route needed by your admin panel status dropdown
Route::put('/aeropay/transactions/{code}/status', [TransactionController::class, 'updateStatus']);