<?php

namespace Database\Seeders\aureliya;

use Illuminate\Database\Seeder;
use App\Models\aureliya\Property;
use App\Models\aureliya\Amenity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PropertyAmenitySeeder extends Seeder
{
    public function run()
    {
        // 1. Connection Config
        $connection = 'aureliya'; 

        // 2. Disable Foreign Key Checks (Speed up & Safety)
        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0;');

        // 3. Create Table if Missing
        if (!Schema::connection($connection)->hasTable('property_amenities')) {
            Schema::connection($connection)->create('property_amenities', function ($table) {
                $table->string('property_id', 36);
                $table->string('amenity_id', 36);
                $table->primary(['property_id', 'amenity_id']);
            });
        }

        // 4. Clear Old Data (Truncate is faster than Delete)
        DB::connection($connection)->table('property_amenities')->truncate();

        // 5. Get Data Efficiently
        // Kukunin lang natin ang ID para mas mabilis, hindi ang buong Model
        $propertyIds = Property::pluck('_id'); 
        $amenityIds = Amenity::pluck('_id');

        if ($propertyIds->isEmpty() || $amenityIds->isEmpty()) {
            $this->command->warn("⚠️ No properties or amenities found.");
            return;
        }

        $this->command->info("Linking amenities to " . $propertyIds->count() . " properties...");

        // 6. Build the Insert Array (In Memory)
        $data = [];
        foreach ($propertyIds as $propId) {
            // Pick random 3 to 7 amenities
            $randomKeys = $amenityIds->random(min($amenityIds->count(), rand(3, 7)));

            foreach ($randomKeys as $amenityId) {
                $data[] = [
                    'property_id' => $propId,
                    'amenity_id'  => $amenityId
                ];
            }
        }

        // 7. Batch Insert (The Speed Fix!)
        // I-iinsert natin ng 1,000 rows per query instead na isa-isa
        $chunks = array_chunk($data, 1000);
        $totalChunks = count($chunks);
        
        foreach ($chunks as $index => $chunk) {
            DB::connection($connection)->table('property_amenities')->insertOrIgnore($chunk);
            // Optional: Progress bar effect sa terminal
            $this->command->info("Inserted chunk " . ($index + 1) . " of $totalChunks");
        }

        // 8. Re-enable Foreign Keys
        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info("✅ DONE! Successfully linked amenities.");
    }
}