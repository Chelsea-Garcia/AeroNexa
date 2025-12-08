<?php

namespace App\Models\psa;

use MongoDB\Laravel\Eloquent\Model; // <--- IMPORTANTE: Gamitin ang MongoDB Model
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passenger extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'passengers';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'birthdate',
        'gender',
        'nationality',
        'civil_status',
        'passport_number',
        'passport_expiry',
        'special_assistance',
        'emergency_contact_name',
        'emergency_contact_number',
        'type'
    ];
}