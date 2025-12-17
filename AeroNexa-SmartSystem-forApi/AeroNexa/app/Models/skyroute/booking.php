<?php
namespace App\Models\skyroute;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $connection = 'mongodb_skyroute';
    protected $collection = 'bookings';

    protected $fillable = [
        'user_id', 
        'vehicle_id', 
        'vehicle_name',      
        'vehicle_plate',     
        'origin_location_id', 
        'destination_location_id', 
        'date', 
        'time', 
        'passengers', 
        'status',
        'total_price',       // <--- ADD THIS
        'transaction_code'   // <--- ADD THIS
    ];
}