<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->decimal('light_intensity', 10, 2)->nullable()->after('soil_ph'); // lux
            $table->boolean('rain_detected')->default(false)->after('light_intensity'); // boolean
        });
    }

    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn(['light_intensity', 'rain_detected']);
        });
    }
};
