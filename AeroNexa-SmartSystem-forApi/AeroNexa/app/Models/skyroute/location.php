<?php

namespace App\Models\skyroute;

use MongoDB\Laravel\Eloquent\Model;

class Location extends Model
{
    // Ensure this matches 'mongodb_skyroute' in your database.php
    protected $connection = 'mongodb_skyroute';
    protected $collection = 'locations'; 
    protected $guarded = [];
}