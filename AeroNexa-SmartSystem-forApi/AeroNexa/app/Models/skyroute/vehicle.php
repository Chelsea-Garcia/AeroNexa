<?php

namespace App\Models\skyroute;

use MongoDB\Laravel\Eloquent\Model;

class Vehicle extends Model
{
    protected $connection = 'mongodb_skyroute';
    protected $collection = 'vehicles';
    
    // 🔴 FIX: Define Primary Key to help find() work with Strings/ObjectIds
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];
}