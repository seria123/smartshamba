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
            $table->string('rainfall_zone')->nullable()->after('location');
            $table->string('topography')->nullable()->after('rainfall_zone');
            $table->string('water_source')->nullable()->after('topography');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn(['rainfall_zone', 'topography', 'water_source']);
        });
    }
};
