<?php

namespace Database\Seeders\aureliya;

use Illuminate\Database\Seeder;
use App\Models\aureliya\Amenity;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run()
    {
        $amenities = [
            'Wifi', 'Free parking', 'Pool', 'Hot tub', 'Kitchen', 
            'Air conditioning', 'Washer', 'Dryer', 'TV', 'Gym', 
            'Breakfast included', 'Ocean view', 'Pet-friendly', '24/7 Security',
        ];

        foreach ($amenities as $name) {
            // Check if exists first to avoid duplicates
            if (!Amenity::where('name', $name)->exists()) {
                Amenity::create([
                    '_id' => (string) Str::uuid(),
                    'name' => $name
                ]);
            }
        }
    }
}