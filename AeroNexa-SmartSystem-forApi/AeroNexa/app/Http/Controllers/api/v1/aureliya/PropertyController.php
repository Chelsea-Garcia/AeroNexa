<?php

namespace App\Http\Controllers\api\v1\aureliya;

use App\Http\Controllers\Controller;
use App\Models\aureliya\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        try {
            // 1. Fetch properties directly using the Aureliya Model
            // (The Model handles the connection to 'aureliya' database)
            $properties = Property::orderBy('type', 'asc')->get();

            // 2. Return Data
            return response()->json($properties);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error Loading Properties',
                'technical_error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}