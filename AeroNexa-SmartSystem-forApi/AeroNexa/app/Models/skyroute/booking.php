<?php
namespace App\Models\skyroute;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;
    // CHANGE THIS LINE:
    protected $connection = 'mongodb_skyroute';
    protected $collection = 'bookings';
    protected $fillable = [
        'user_id', 'vehicle_id', 'origin_location_id', 'destination_location_id', 
        'date', 'time', 'passengers', 'status'
    ];
}