<?php

namespace App\Http\Controllers\api\v1\aureliya; // FIX: Lowercase namespace

use App\Http\Controllers\Controller;
use App\Models\aureliya\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        return Amenity::all();
    }

    public function show($id)
    {
        return Amenity::findOrFail($id);
    }
}