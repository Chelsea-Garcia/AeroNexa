<?php

namespace App\Models\psa;

// USE MONGODB MODEL (Importante ito!)
use MongoDB\Laravel\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    // --- CRITICAL FIX: ITURO SA MONGODB ---
    protected $connection = 'mongodb'; 
    protected $collection = 'bookings'; 

    protected $fillable = [
        'flight_id',
        'user_id',
        'passenger_id',
        'flight_date',
        'status',
        'seat_number',
        'booking_date'
    ];
}