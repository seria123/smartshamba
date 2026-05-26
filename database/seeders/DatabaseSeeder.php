<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Crop;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed crops and livestock types first
        $this->call([
            CropSeeder::class,
            LivestockFeedSeeder::class,
            CropCycleSeeder::class,
            BuyerSeeder::class,
        ]);

        // Create users (update if already exists)
        // Create/update users
        User::updateOrCreate(
            ['email' => 'admin@smartshamba.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@smartshamba.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'stephenthuku183@gmail.com'],
            [
                'name' => 'Stephen Thuku (Admin)',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@smartshamba.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'stephenthuku183@gmail.com'],
            [
                'name' => 'Stephen Thuku (Admin)',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Get the admin user for associations
        $admin = User::where('email', 'admin@smartshamba.com')->first();

        // Create farms
        $farm1 = Farm::firstOrCreate(
            ['name' => 'Green Valley Farm'],
            [
                'user_id' => $admin->id,
                'location' => 'Nairobi, Kenya',
                'size_hectares' => 50.5,
                'description' => 'A large-scale agricultural farm specializing in vegetables and cereals.',
            ]
        );

        $farm2 = Farm::firstOrCreate(
            ['name' => 'Sunrise Plantation'],
            [
                'user_id' => $admin->id,
                'location' => 'Mombasa, Kenya',
                'size_hectares' => 25.0,
                'description' => 'A tropical fruit farm with mangoes, bananas, and pineapples.',
            ]
        );

        $farm3 = Farm::firstOrCreate(
            ['name' => 'Highland Organics'],
            [
                'user_id' => $admin->id,
                'location' => 'Nakuru, Kenya',
                'size_hectares' => 35.75,
                'description' => 'An organic farming operation focused on sustainable practices.',
            ]
        );

        // Create fields
        $field1 = Field::firstOrCreate(
            ['name' => 'North Field', 'farm_id' => $farm1->id],
            [
                'user_id' => $admin->id,
                'size_hectares' => 15.0,
                'location' => 'North Section',
                'description' => 'Primary vegetable cultivation area.',
            ]
        );

        $field2 = Field::firstOrCreate(
            ['name' => 'South Field', 'farm_id' => $farm1->id],
            [
                'user_id' => $admin->id,
                'size_hectares' => 20.5,
                'location' => 'South Section',
                'description' => 'Cereal crops and maize cultivation.',
            ]
        );

        $field3 = Field::firstOrCreate(
            ['name' => 'Mango Orchard', 'farm_id' => $farm2->id],
            [
                'user_id' => $admin->id,
                'size_hectares' => 10.0,
                'location' => 'East Section',
                'description' => 'Mango trees variety Kent and Tommy Atkins.',
            ]
        );

        $field4 = Field::firstOrCreate(
            ['name' => 'Banana Grove', 'farm_id' => $farm2->id],
            [
                'user_id' => $admin->id,
                'size_hectares' => 8.0,
                'location' => 'West Section',
                'description' => 'Banana plantation with Cavendish variety.',
            ]
        );

        $field5 = Field::firstOrCreate(
            ['name' => 'Organic Vegetables', 'farm_id' => $farm3->id],
            [
                'user_id' => $admin->id,
                'size_hectares' => 12.0,
                'location' => 'Central Section',
                'description' => 'Certified organic vegetables - tomatoes, peppers, onions.',
            ]
        );

        // Create crops
        $crop1 = Crop::firstOrCreate(
            ['name' => 'Tomatoes', 'field_id' => $field1->id, 'variety' => 'Roma'],
            [
                'user_id' => $admin->id,
                'category' => 'Vegetables',
                'planting_date' => '2024-01-15',
                'expected_harvest_date' => '2024-04-15',
                'notes' => 'Regular watering required. Monitor for pests.',
            ]
        );

        $crop2 = Crop::firstOrCreate(
            ['name' => 'Maize', 'field_id' => $field2->id, 'variety' => 'H614'],
            [
                'user_id' => $admin->id,
                'category' => 'Cereals',
                'planting_date' => '2024-03-01',
                'expected_harvest_date' => '2024-08-01',
                'notes' => 'Drought resistant variety.',
            ]
        );

        $crop3 = Crop::firstOrCreate(
            ['name' => 'Mangoes', 'field_id' => $field3->id, 'variety' => 'Kent'],
            [
                'user_id' => $admin->id,
                'category' => 'Fruits',
                'planting_date' => '2023-04-01',
                'expected_harvest_date' => '2024-12-01',
                'notes' => 'Young orchard, first significant harvest expected.',
            ]
        );

        $crop4 = Crop::firstOrCreate(
            ['name' => 'Bananas', 'field_id' => $field4->id, 'variety' => 'Cavendish'],
            [
                'user_id' => $admin->id,
                'category' => 'Fruits',
                'planting_date' => '2023-06-15',
                'expected_harvest_date' => '2024-06-15',
                'notes' => 'Regular harvesting cycle.',
            ]
        );

        $crop5 = Crop::firstOrCreate(
            ['name' => 'Mixed Vegetables', 'field_id' => $field5->id, 'variety' => 'Various'],
            [
                'user_id' => $admin->id,
                'category' => 'Vegetables',
                'planting_date' => '2024-02-01',
                'expected_harvest_date' => '2024-05-01',
                'notes' => 'Certified organic by KEBS.',
            ]
        );

        // Create sensors
        $sensor1 = Sensor::firstOrCreate(
            ['serial_number' => 'SNS-001-2024'],
            [
                'field_id' => $field1->id,
                'name' => 'Tomato Soil Sensor 1',
                'type' => 'multi',
                'status' => 'active',
                'description' => 'Multi-sensor for soil monitoring in tomato field.',
            ]
        );

        $sensor2 = Sensor::firstOrCreate(
            ['serial_number' => 'SNS-002-2024'],
            [
                'field_id' => $field1->id,
                'name' => 'Tomato Weather Station',
                'type' => 'temperature',
                'status' => 'active',
                'description' => 'Temperature and humidity monitoring.',
            ]
        );

        $sensor3 = Sensor::firstOrCreate(
            ['serial_number' => 'SNS-003-2024'],
            [
                'field_id' => $field2->id,
                'name' => 'Maize Field Sensor',
                'type' => 'soil_moisture',
                'status' => 'active',
                'description' => 'Soil moisture monitoring for maize.',
            ]
        );

        $sensor4 = Sensor::firstOrCreate(
            ['serial_number' => 'SNS-004-2024'],
            [
                'field_id' => $field3->id,
                'name' => 'Mango Orchard Sensor',
                'type' => 'multi',
                'status' => 'active',
                'description' => 'Multi-sensor for mango orchard.',
            ]
        );

        $sensor5 = Sensor::firstOrCreate(
            ['serial_number' => 'SNS-005-2024'],
            [
                'field_id' => $field5->id,
                'name' => 'Organic Field pH Sensor',
                'type' => 'soil_ph',
                'status' => 'active',
                'description' => 'Soil pH monitoring for organic vegetables.',
            ]
        );

        // Create sensor readings (delete old ones for this seeder run to avoid duplicates)
        SensorReading::where('sensor_id', $sensor1->id)->delete();
        for ($i = 0; $i < 10; $i++) {
            SensorReading::create([
                'sensor_id' => $sensor1->id,
                'soil_moisture' => 45.0 + rand(-10, 10),
                'temperature' => 25.0 + rand(-5, 5),
                'humidity' => 60.0 + rand(-10, 10),
                'soil_ph' => 6.5 + rand(-0.5, 0.5),
                'timestamp' => now()->subHours($i * 2),
            ]);
        }

        SensorReading::where('sensor_id', $sensor2->id)->delete();
        for ($i = 0; $i < 10; $i++) {
            SensorReading::create([
                'sensor_id' => $sensor2->id,
                'soil_moisture' => null,
                'temperature' => 28.0 + rand(-5, 5),
                'humidity' => 55.0 + rand(-10, 10),
                'soil_ph' => null,
                'timestamp' => now()->subHours($i * 2),
            ]);
        }

        SensorReading::where('sensor_id', $sensor3->id)->delete();
        for ($i = 0; $i < 10; $i++) {
            SensorReading::create([
                'sensor_id' => $sensor3->id,
                'soil_moisture' => 35.0 + rand(-10, 10),
                'temperature' => null,
                'humidity' => null,
                'soil_ph' => null,
                'timestamp' => now()->subHours($i * 2),
            ]);
        }

        SensorReading::where('sensor_id', $sensor4->id)->delete();
        for ($i = 0; $i < 10; $i++) {
            SensorReading::create([
                'sensor_id' => $sensor4->id,
                'soil_moisture' => 50.0 + rand(-10, 10),
                'temperature' => 30.0 + rand(-5, 5),
                'humidity' => 70.0 + rand(-10, 10),
                'soil_ph' => 6.0 + rand(-0.5, 0.5),
                'timestamp' => now()->subHours($i * 2),
            ]);
        }

        SensorReading::where('sensor_id', $sensor5->id)->delete();
        for ($i = 0; $i < 10; $i++) {
            SensorReading::create([
                'sensor_id' => $sensor5->id,
                'soil_moisture' => null,
                'temperature' => null,
                'humidity' => null,
                'soil_ph' => 6.8 + rand(-0.3, 0.3),
                'timestamp' => now()->subHours($i * 2),
            ]);
        }

        // Create alerts
        // Get some sensor readings to link alerts to
        $reading1 = SensorReading::where('sensor_id', $sensor1->id)->first();
        $reading2 = SensorReading::where('sensor_id', $sensor3->id)->first();
        $reading3 = SensorReading::where('sensor_id', $sensor4->id)->first();
        $reading4 = SensorReading::where('sensor_id', $sensor5->id)->first();

        // Create sample livestock (linked to admin user)
        $cowType = LivestockType::where('slug', 'cow')->first();
        $goatType = LivestockType::where('slug', 'goat')->first();
        $chickenType = LivestockType::where('slug', 'chicken')->first();
        $sheepType = LivestockType::where('slug', 'sheep')->first();

        if ($cowType) {
            Livestock::firstOrCreate(
                ['tag_number' => 'COW-001'],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $cowType->id,
                    'farm_id' => $farm1->id,
                    'name' => 'Bessie',
                    'birth_date' => '2022-03-15',
                    'gender' => 'female',
                    'status' => 'healthy',
                ]
            );
            Livestock::firstOrCreate(
                ['tag_number' => 'COW-002'],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $cowType->id,
                    'farm_id' => $farm1->id,
                    'name' => 'Buttercup',
                    'birth_date' => '2023-01-20',
                    'gender' => 'female',
                    'status' => 'healthy',
                ]
            );
        }

        if ($goatType) {
            Livestock::firstOrCreate(
                ['tag_number' => 'GOAT-001'],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $goatType->id,
                    'farm_id' => $farm2->id,
                    'name' => 'Billy',
                    'birth_date' => '2023-05-10',
                    'gender' => 'male',
                    'status' => 'healthy',
                ]
            );
        }

        if ($chickenType) {
            Livestock::firstOrCreate(
                ['tag_number' => null],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $chickenType->id,
                    'farm_id' => $farm2->id,
                    'name' => 'Henny',
                    'birth_date' => '2024-02-01',
                    'gender' => 'female',
                    'status' => 'healthy',
                ]
            );
        }

        if ($sheepType) {
            Livestock::firstOrCreate(
                ['tag_number' => 'SHEEP-001'],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $sheepType->id,
                    'farm_id' => $farm3->id,
                    'name' => 'Dolly',
                    'birth_date' => '2023-08-12',
                    'gender' => 'female',
                    'status' => 'healthy',
                ]
            );
        }

        if ($goatType) {
            Livestock::firstOrCreate(
                ['tag_number' => 'GOAT-001'],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $goatType->id,
                    'farm_id' => $farm2->id,
                    'name' => 'Billy',
                    'birth_date' => '2023-05-10',
                    'gender' => 'male',
                    'status' => 'healthy',
                ]
            );
        }

        if ($chickenType) {
            Livestock::firstOrCreate(
                ['tag_number' => null],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $chickenType->id,
                    'farm_id' => $farm2->id,
                    'name' => 'Henny',
                    'birth_date' => '2024-02-01',
                    'gender' => 'female',
                    'status' => 'healthy',
                ]
            );
        }

        if ($sheepType) {
            Livestock::firstOrCreate(
                ['tag_number' => 'SHEEP-001'],
                [
                    'user_id' => $admin->id,
                    'livestock_type_id' => $sheepType->id,
                    'farm_id' => $farm3->id,
                    'name' => 'Dolly',
                    'birth_date' => '2023-08-12',
                    'gender' => 'female',
                    'status' => 'healthy',
                ]
            );
        }

        // Delete old alerts for this seeder's sensors and recreate
        Alert::whereIn('sensor_reading_id', function ($query) use ($sensor1, $sensor2, $sensor3, $sensor4, $sensor5) {
            $query->select('id')
                ->from('sensor_readings')
                ->whereIn('sensor_id', [$sensor1->id, $sensor2->id, $sensor3->id, $sensor4->id, $sensor5->id]);
        })->delete();

        // Get fresh sensor readings
        $reading1 = SensorReading::where('sensor_id', $sensor1->id)->first();
        $reading2 = SensorReading::where('sensor_id', $sensor3->id)->first();
        $reading3 = SensorReading::where('sensor_id', $sensor4->id)->first();
        $reading4 = SensorReading::where('sensor_id', $sensor5->id)->first();

        Alert::create([
            'sensor_reading_id' => $reading1->id,
            'type' => 'warning',
            'severity' => 'medium',
            'message' => 'Soil moisture level below optimal threshold. Consider irrigation.',
            'parameter' => 'soil_moisture',
            'value' => $reading1->soil_moisture,
            'threshold' => 40.0,
            'is_read' => false,
        ]);

        Alert::create([
            'sensor_reading_id' => $reading2->id,
            'type' => 'critical',
            'severity' => 'high',
            'message' => 'Critical low soil moisture detected! Immediate irrigation required.',
            'parameter' => 'soil_moisture',
            'value' => $reading2->soil_moisture,
            'threshold' => 30.0,
            'is_read' => false,
        ]);

        Alert::create([
            'sensor_reading_id' => $reading3->id,
            'type' => 'info',
            'severity' => 'low',
            'message' => 'Temperature readings are within normal range.',
            'parameter' => 'temperature',
            'value' => $reading3->temperature,
            'threshold' => 35.0,
            'is_read' => true,
        ]);

        Alert::create([
            'sensor_reading_id' => $reading4->id,
            'type' => 'warning',
            'severity' => 'medium',
            'message' => 'Soil pH level slightly acidic. Monitor closely.',
            'parameter' => 'soil_ph',
            'value' => $reading4->soil_ph,
            'threshold' => 7.0,
            'is_read' => false,
        ]);

        Alert::create([
            'sensor_reading_id' => $reading1->id,
            'type' => 'info',
            'severity' => 'low',
            'message' => 'Humidity levels are optimal for crop growth.',
            'parameter' => 'humidity',
            'value' => $reading1->humidity,
            'threshold' => 80.0,
            'is_read' => true,
        ]);
    }
}
