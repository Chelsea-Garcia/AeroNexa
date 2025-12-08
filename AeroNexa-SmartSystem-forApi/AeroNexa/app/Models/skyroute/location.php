<?php
namespace App\Models\skyroute;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    // CHANGE THIS LINE:
    protected $connection = 'mongodb_skyroute'; 
    protected $collection = 'locations';
    protected $fillable = ['name', 'address'];
}