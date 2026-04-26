<?php

namespace Database\Seeders;

use App\Models\FeedType;
use App\Models\LivestockDisease;
use App\Models\LivestockType;
use Illuminate\Database\Seeder;

class LivestockFeedSeeder extends Seeder
{
    public function run(): void
    {
        $livestockTypes = [
            ['name' => 'Cow', 'slug' => 'cow', 'description' => 'Domesticated cattle', 'requires_individual_tracking' => true],
            ['name' => 'Goat', 'slug' => 'goat', 'description' => 'Small ruminant', 'requires_individual_tracking' => false],
            ['name' => 'Chicken', 'slug' => 'chicken', 'description' => 'Poultry for eggs and meat', 'requires_individual_tracking' => false],
            ['name' => 'Sheep', 'slug' => 'sheep', 'description' => 'Wool and meat producing', 'requires_individual_tracking' => false],
        ];

        foreach ($livestockTypes as $type) {
            LivestockType::firstOrCreate(['slug' => $type['slug']], $type);
        }

        $feedTypes = [
            ['name' => 'Hay', 'default_unit' => 'kg', 'min_threshold' => 50, 'description' => 'Dried grass for roughage'],
            ['name' => 'Maize Bran', 'default_unit' => 'kg', 'min_threshold' => 100, 'description' => 'Corn by-product'],
            ['name' => 'Silage', 'default_unit' => 'kg', 'min_threshold' => 200, 'description' => 'Fermented forage'],
            ['name' => 'Chicken Feed', 'default_unit' => 'kg', 'min_threshold' => 25, 'description' => 'Commercial poultry feed'],
            ['name' => 'Concentrate', 'default_unit' => 'kg', 'min_threshold' => 20, 'description' => 'High protein supplement'],
        ];

        foreach ($feedTypes as $feed) {
            FeedType::firstOrCreate(['name' => $feed['name']], $feed);
        }

        // Only seed diseases if livestock exist
        // Commented out until livestock are seeded first
        /*
        $diseases = [
            [
                'name' => 'Foot and Mouth Disease',
                'species' => 'Cattle',
                'cause' => 'Virus',
                'symptoms' => 'Blisters on mouth and feet, drooling, lameness',
                'transmission' => 'Direct contact, contaminated equipment',
                'prevention' => 'Vaccination, quarantine',
                'treatment' => 'Supportive care',
                'mortality_rate' => 5.00,
                'severity' => 'high',
            ],
            [
                'name' => 'Mastitis',
                'species' => 'Cattle',
                'cause' => 'Bacteria',
                'symptoms' => 'Swollen udder, pain, abnormal milk',
                'transmission' => 'Dirty milking equipment',
                'prevention' => 'Hygiene during milking',
                'treatment' => 'Antibiotics',
                'mortality_rate' => 1.00,
                'severity' => 'medium',
            ],
            [
                'name' => 'Newcastle Disease',
                'species' => 'Poultry',
                'cause' => 'Virus',
                'symptoms' => 'Sneezing, paralysis, drop in egg production',
                'transmission' => 'Airborne, contaminated feed',
                'prevention' => 'Vaccination',
                'treatment' => 'No cure',
                'mortality_rate' => 50.00,
                'severity' => 'high',
            ],
        ];

        foreach ($diseases as $disease) {
            // Requires livestock to exist first
            // LivestockDisease::create($disease);
        }
        */
    }
}
