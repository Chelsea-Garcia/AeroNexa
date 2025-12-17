<?php

namespace App\Models\aureliya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    // FIX: Match the config connection name
    protected $connection = 'aureliya'; 
    protected $table = 'amenities';
    
    protected $primaryKey = "_id";
    public $incrementing = false;
    protected $keyType = "string";
    public $timestamps = false;

    protected $fillable = [
        '_id',
        'name',
    ];

    public function properties()
    {
        return $this->belongsToMany(
            Property::class,
            'property_amenities',
            'amenity_id',
            'property_id'
        );
    }
}