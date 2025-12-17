<?php

namespace App\Http\Controllers\api\v1\skyroute;

use App\Http\Controllers\Controller;
use App\Models\skyroute\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // 1. Get Unique Countries
    public function getCountries() {
        // Safe Way: Fetch all, pluck country, remove duplicates
        $countries = Location::all()
            ->pluck('country')
            ->unique()
            ->sort()    // <--- Sorts A-Z
            ->values(); // Re-indexes array
        return response()->json($countries);
    }

    // 2. Get Divisions for a Country
    public function getDivisions($country) {
        $divisions = Location::where('country', $country)
            ->get()
            ->pluck('division')
            ->unique()
            ->sort()    // <--- Sorts A-Z
            ->values();
        return response()->json($divisions);
    }

    // 3. Get Cities for a Division
    public function getCities($division) {
        // We need 'city' for the name and '_id' is automatic
        $cities = Location::where('division', $division)
            ->orderBy('city', 'asc') // <--- Database Sort
            ->get(['city']);
        return response()->json($cities);
    }
}