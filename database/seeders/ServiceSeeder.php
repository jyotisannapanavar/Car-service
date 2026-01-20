<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Basic Wash',
                'description' => 'Exterior wash with hand dry',
                'base_price' => 15.00,
                'duration_minutes' => 20,
            ],
            [
                'name' => 'Premium Wash',
                'description' => 'Exterior wash, interior vacuum, and dashboard wipe',
                'base_price' => 30.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Full Detail',
                'description' => 'Complete interior and exterior detailing',
                'base_price' => 150.00,
                'duration_minutes' => 180,
            ],
            [
                'name' => 'Interior Cleaning',
                'description' => 'Deep interior cleaning and sanitization',
                'base_price' => 50.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Wax & Polish',
                'description' => 'Hand wax and polish for exterior shine',
                'base_price' => 75.00,
                'duration_minutes' => 90,
            ],
            [
                'name' => 'Engine Cleaning',
                'description' => 'Engine bay cleaning and degreasing',
                'base_price' => 40.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Tire & Wheel Cleaning',
                'description' => 'Deep cleaning of tires and wheels',
                'base_price' => 25.00,
                'duration_minutes' => 20,
            ],
            [
                'name' => 'Headlight Restoration',
                'description' => 'Restore cloudy or yellowed headlights',
                'base_price' => 60.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Ceramic Coating',
                'description' => 'Professional ceramic coating application',
                'base_price' => 500.00,
                'duration_minutes' => 480,
            ],
            [
                'name' => 'Paint Protection Film',
                'description' => 'Clear bra installation for paint protection',
                'base_price' => 800.00,
                'duration_minutes' => 360,
            ],
        ];

        $organization = Organization::first();

        if ($organization) {
            foreach ($services as $service) {
                Service::firstOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'name' => $service['name'],
                    ],
                    array_merge($service, ['organization_id' => $organization->id])
                );
            }
        }
    }
}
