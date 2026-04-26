<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->string('soil_type')->nullable()->after('description');
            $table->decimal('gps_latitude', 10, 8)->nullable()->after('soil_type');
            $table->decimal('gps_longitude', 11, 8)->nullable()->after('gps_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn(['soil_type', 'gps_latitude', 'gps_longitude']);
        });
    }
};
