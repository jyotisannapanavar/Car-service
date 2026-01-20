<?php

namespace Database\Seeders;

use App\Models\VehicleBrand;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleBrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW',
            'Mercedes-Benz', 'Audi', 'Volkswagen', 'Nissan', 'Hyundai',
            'Kia', 'Mazda', 'Subaru', 'Lexus', 'Jeep',
            'Tesla', 'Porsche', 'Land Rover', 'Volvo', 'Mitsubishi',
        ];

        $vehicleTypes = VehicleType::whereIn('name', ['Sedan', 'SUV', 'Hatchback', 'Truck'])->get();

        foreach ($vehicleTypes as $type) {
            foreach ($brands as $brand) {
                VehicleBrand::firstOrCreate(
                    [
                        'vehicle_type_id' => $type->id,
                        'name' => $brand,
                    ],
                    [
                        'vehicle_type_id' => $type->id,
                        'name' => $brand,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
