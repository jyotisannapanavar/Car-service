<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleTypes = [
            ['name' => 'Sedan', 'description' => 'Standard four-door passenger car'],
            ['name' => 'SUV', 'description' => 'Sport Utility Vehicle'],
            ['name' => 'Hatchback', 'description' => 'Compact car with rear door that swings upward'],
            ['name' => 'Truck', 'description' => 'Pickup truck or commercial truck'],
            ['name' => 'Van', 'description' => 'Passenger or cargo van'],
            ['name' => 'Coupe', 'description' => 'Two-door car with fixed roof'],
            ['name' => 'Convertible', 'description' => 'Car with retractable roof'],
            ['name' => 'Wagon', 'description' => 'Station wagon or estate car'],
            ['name' => 'Motorcycle', 'description' => 'Two-wheeled motor vehicle'],
            ['name' => 'Bus', 'description' => 'Large passenger vehicle'],
        ];

        foreach ($vehicleTypes as $type) {
            VehicleType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
