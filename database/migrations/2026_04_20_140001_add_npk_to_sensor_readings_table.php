<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->decimal('nitrogen_level', 6, 2)->nullable()->after('light_intensity');
            $table->decimal('phosphorus_level', 6, 2)->nullable()->after('nitrogen_level');
            $table->decimal('potassium_level', 6, 2)->nullable()->after('phosphorus_level');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn(['nitrogen_level', 'phosphorus_level', 'potassium_level']);
        });
    }
};
