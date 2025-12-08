<?php
namespace App\Models\skyroute;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;
    // CHANGE THIS LINE:
    protected $connection = 'mongodb_skyroute';
    protected $collection = 'vehicles';
    protected $fillable = ['location_id', 'name', 'type', 'plate_number', 'capacity'];
}