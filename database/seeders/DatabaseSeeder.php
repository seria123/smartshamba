<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Crop;
use App\Models\Farm;
use App\Models\Field;
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
        // Create users
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@smartshamba.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@smartshamba.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'User',
            'email' => 'stephenthuku183@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Create farms
        $farm1 = Farm::create([
            'user_id' => 1,
            'name' => 'Green Valley Farm',
            'location' => 'Nairobi, Kenya',
            'size_hectares' => 50.5,
            'description' => 'A large-scale agricultural farm specializing in vegetables and cereals.',
        ]);

        $farm2 = Farm::create([
            'user_id' => 1,
            'name' => 'Sunrise Plantation',
            'location' => 'Mombasa, Kenya',
            'size_hectares' => 25.0,
            'description' => 'A tropical fruit farm with mangoes, bananas, and pineapples.',
        ]);

        $farm3 = Farm::create([
            'user_id' => 1,
            'name' => 'Highland Organics',
            'location' => 'Nakuru, Kenya',
            'size_hectares' => 35.75,
            'description' => 'An organic farming operation focused on sustainable practices.',
        ]);

        // Create fields
        $field1 = Field::create([
            'user_id' => 1,
            'farm_id' => $farm1->id,
            'name' => 'North Field',
            'size_hectares' => 15.0,
            'location' => 'North Section',
            'description' => 'Primary vegetable cultivation area.',
        ]);

        $field2 = Field::create([
            'user_id' => 1,
            'farm_id' => $farm1->id,
            'name' => 'South Field',
            'size_hectares' => 20.5,
            'location' => 'South Section',
            'description' => 'Cereal crops and maize cultivation.',
        ]);

        $field3 = Field::create([
            'user_id' => 1,
            'farm_id' => $farm2->id,
            'name' => 'Mango Orchard',
            'size_hectares' => 10.0,
            'location' => 'East Section',
            'description' => 'Mango trees variety Kent and Tommy Atkins.',
        ]);

        $field4 = Field::create([
            'user_id' => 1,
            'farm_id' => $farm2->id,
            'name' => 'Banana Grove',
            'size_hectares' => 8.0,
            'location' => 'West Section',
            'description' => 'Banana plantation with Cavendish variety.',
        ]);

        $field5 = Field::create([
            'user_id' => 1,
            'farm_id' => $farm3->id,
            'name' => 'Organic Vegetables',
            'size_hectares' => 12.0,
            'location' => 'Central Section',
            'description' => 'Certified organic vegetables - tomatoes, peppers, onions.',
        ]);

        // Create crops
        $crop1 = Crop::create([
            'user_id' => 1,
            'field_id' => $field1->id,
            'name' => 'Tomatoes',
            'variety' => 'Roma',
            'planting_date' => '2024-01-15',
            'expected_harvest_date' => '2024-04-15',
            'notes' => 'Regular watering required. Monitor for pests.',
        ]);

        $crop2 = Crop::create([
            'user_id' => 1,
            'field_id' => $field2->id,
            'name' => 'Maize',
            'variety' => 'H614',
            'planting_date' => '2024-03-01',
            'expected_harvest_date' => '2024-08-01',
            'notes' => 'Drought resistant variety.',
        ]);

        $crop3 = Crop::create([
            'user_id' => 1,
            'field_id' => $field3->id,
            'name' => 'Mangoes',
            'variety' => 'Kent',
            'planting_date' => '2023-04-01',
            'expected_harvest_date' => '2024-12-01',
            'notes' => 'Young orchard, first significant harvest expected.',
        ]);

        $crop4 = Crop::create([
            'user_id' => 1,
            'field_id' => $field4->id,
            'name' => 'Bananas',
            'variety' => 'Cavendish',
            'planting_date' => '2023-06-15',
            'expected_harvest_date' => '2024-06-15',
            'notes' => 'Regular harvesting cycle.',
        ]);

        $crop5 = Crop::create([
            'user_id' => 1,
            'field_id' => $field5->id,
            'name' => 'Mixed Vegetables',
            'variety' => 'Various',
            'planting_date' => '2024-02-01',
            'expected_harvest_date' => '2024-05-01',
            'notes' => 'Certified organic by KEBS.',
        ]);

        // Create sensors
        $sensor1 = Sensor::create([
            'field_id' => $field1->id,
            'name' => 'Tomato Soil Sensor 1',
            'type' => 'multi',
            'serial_number' => 'SNS-001-2024',
            'status' => 'active',
            'description' => 'Multi-sensor for soil monitoring in tomato field.',
        ]);

        $sensor2 = Sensor::create([
            'field_id' => $field1->id,
            'name' => 'Tomato Weather Station',
            'type' => 'temperature',
            'serial_number' => 'SNS-002-2024',
            'status' => 'active',
            'description' => 'Temperature and humidity monitoring.',
        ]);

        $sensor3 = Sensor::create([
            'field_id' => $field2->id,
            'name' => 'Maize Field Sensor',
            'type' => 'soil_moisture',
            'serial_number' => 'SNS-003-2024',
            'status' => 'active',
            'description' => 'Soil moisture monitoring for maize.',
        ]);

        $sensor4 = Sensor::create([
            'field_id' => $field3->id,
            'name' => 'Mango Orchard Sensor',
            'type' => 'multi',
            'serial_number' => 'SNS-004-2024',
            'status' => 'active',
            'description' => 'Multi-sensor for mango orchard.',
        ]);

        $sensor5 = Sensor::create([
            'field_id' => $field5->id,
            'name' => 'Organic Field pH Sensor',
            'type' => 'soil_ph',
            'serial_number' => 'SNS-005-2024',
            'status' => 'active',
            'description' => 'Soil pH monitoring for organic vegetables.',
        ]);

        // Create sensor readings
        // Sensor 1 - Multi sensor readings
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

        // Sensor 2 - Temperature readings
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

        // Sensor 3 - Soil moisture readings
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

        // Sensor 4 - Multi sensor readings
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

        // Sensor 5 - pH readings
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
