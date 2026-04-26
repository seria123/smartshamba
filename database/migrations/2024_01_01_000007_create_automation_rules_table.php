<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sensor_type'); // soil_moisture, temperature, humidity, etc.
            $table->string('operator'); // <, <=, >, >=, ==, !=
            $table->decimal('threshold', 10, 2);
            $table->string('action'); // irrigation_on, irrigation_off, cooling_on, cooling_off, shade_on, shade_off, alert
            $table->boolean('is_active')->default(true);
            $table->integer('cooldown_minutes')->default(30); // Minimum time between actions
            $table->timestamp('last_triggered')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
