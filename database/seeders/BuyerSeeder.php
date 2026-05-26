<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Farm;
use Illuminate\Database\Seeder;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a farm for buyer associations
        $farm = Farm::first();
        
        // If no farm exists, create a default one
        if (!$farm) {
            $admin = \App\Models\User::where('email', 'admin@smartshamba.com')->first();
            $farm = Farm::create([
                'name' => 'Default Farm',
                'user_id' => $admin ? $admin->id : 1,
                'location' => 'Default Location',
                'size_hectares' => 10.0,
                'description' => 'Default farm for buyer associations',
            ]);
        }

        // Create default buyers if they don't exist
        $buyers = [
            [
                'name' => 'John Doe',
                'company_name' => 'Fresh Market Ltd',
                'email' => 'john@freshmarket.com',
                'phone' => '+254700000001',
                'address' => 'Nairobi, Kenya',
                'buyer_type' => Buyer::TYPE_RETAILER,
                'credit_limit' => 50000.00,
                'rating' => 4.5,
                'notes' => 'Regular buyer of vegetables and fruits',
                'is_active' => true,
                'farm_id' => $farm->id,
            ],
            [
                'name' => 'Jane Smith',
                'company_name' => 'Super Foods Processing',
                'email' => 'jane@superfoods.com',
                'phone' => '+254700000002',
                'address' => 'Mombasa, Kenya',
                'buyer_type' => Buyer::TYPE_PROCESSOR,
                'credit_limit' => 100000.00,
                'rating' => 4.8,
                'notes' => 'Processes agricultural products for export',
                'is_active' => true,
                'farm_id' => $farm->id,
            ],
            [
                'name' => 'Wilson Kipchoge',
                'company_name' => 'Grain Hub Wholesalers',
                'email' => 'wilson@grainhub.com',
                'phone' => '+254700000003',
                'address' => 'Nakuru, Kenya',
                'buyer_type' => Buyer::TYPE_WHOLESALER,
                'credit_limit' => 75000.00,
                'rating' => 4.2,
                'notes' => 'Specializes in cereals and grains',
                'is_active' => true,
                'farm_id' => $farm->id,
            ],
            [
                'name' => 'Amina Hassan',
                'company_name' => 'Kenya Flower Exporters',
                'email' => 'amina@kenyaflower.com',
                'phone' => '+254700000004',
                'address' => 'Nairobi, Kenya',
                'buyer_type' => Buyer::TYPE_EXPORTER,
                'credit_limit' => 200000.00,
                'rating' => 4.9,
                'notes' => 'Exports flowers and horticultural products',
                'is_active' => true,
                'farm_id' => $farm->id,
            ],
            [
                'name' => 'Local Customer',
                'company_name' => null,
                'email' => 'local@customer.com',
                'phone' => '+254700000005',
                'address' => 'Various Locations',
                'buyer_type' => Buyer::TYPE_INDIVIDUAL,
                'credit_limit' => 0.00,
                'rating' => 4.0,
                'notes' => 'Walk-in customers and individual buyers',
                'is_active' => true,
                'farm_id' => $farm->id,
            ],
        ];

        foreach ($buyers as $buyerData) {
            Buyer::updateOrCreate(
                ['email' => $buyerData['email']],
                $buyerData
            );
        }
    }
}