<?php

namespace App\Models\aureliya;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $connection = 'aureliya'; 
    protected $table = 'properties'; 

    // 🔴 IMPORTANT: Do NOT include 'protected $primaryKey'. 
    // Laravel defaults to 'id', which is what we want.
    
    protected $guarded = [];
}