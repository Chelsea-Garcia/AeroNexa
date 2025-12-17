<?php

namespace App\Models\psa;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flight extends Model
{
    use HasFactory;

    // Use 'mongodb_psa' (defined above) or 'mongodb' (generic)
    protected $connection = 'mongodb_psa'; 
    protected $collection = 'flights';
    
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];
}