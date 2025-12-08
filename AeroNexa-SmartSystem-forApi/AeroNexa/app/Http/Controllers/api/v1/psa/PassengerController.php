<?php

namespace App\Http\Controllers\api\v1\psa;

use App\Http\Controllers\Controller;
use App\Models\psa\Passenger; // Siguraduhin na tama ang path ng Model mo
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Passenger::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. UPDATED VALIDATION (Tugma na sa Database mo)
        $validated = $request->validate([
            'user_id' => 'required', // Relaxed validation muna
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string',
            'gender' => 'required|string',
            'civil_status' => 'nullable|string',
            'birthdate' => 'required|date',
            'nationality' => 'required|string',
            
            // Passport
            'passport_number' => 'required|string',
            'passport_expiry' => 'required|date',
            // 'passport_issuer' => 'required', <--- TINANGGAL NA (Wala sa DB)
            
            // New Fields
            'special_assistance' => 'nullable|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_number' => 'required|string',
            'type' => 'required|string'
        ]);

        // 2. CREATE PASSENGER
        // Gamitin ang try-catch para mahuli kung may SQL Error
        try {
            $passenger = Passenger::create([
                'user_id' => $validated['user_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'] ?? 'N/A',
                'gender' => $validated['gender'],
                'civil_status' => $validated['civil_status'] ?? 'Single',
                'birthdate' => $validated['birthdate'],
                'nationality' => $validated['nationality'],
                'passport_number' => $validated['passport_number'],
                'passport_expiry' => $validated['passport_expiry'],
                
                // Siguraduhing may default value kung null
                'special_assistance' => $validated['special_assistance'] ?? 'None',
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_number' => $validated['emergency_contact_number'],
                'type' => $validated['type']
            ]);

            // 3. RETURN SUCCESS WITH ID
            return response()->json([
                'message' => 'Passenger created successfully',
                'id' => $passenger->id, // Explicitly return ID
                'data' => $passenger
            ], 201);

        } catch (\Exception $e) {
            // Kapag may error sa database (e.g. Column not found), lalabas ito dito
            return response()->json([
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Passenger::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $passenger = Passenger::findOrFail($id);
        $passenger->update($request->all());
        return $passenger;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Passenger::destroy($id);
    }
}