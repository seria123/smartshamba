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
        Schema::create('livestock_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_id')->constrained('livestock')->onDelete('cascade');
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('gps_latitude', 10, 8)->nullable(); // -90 to 90
            $table->decimal('gps_longitude', 11, 8)->nullable(); // -180 to 180
            $table->enum('location_type', ['field', 'farm', 'pasture', 'barn', 'transport', 'sick_bay', 'other'])->default('field');
            $table->enum('movement_type', ['grazing', 'resting', 'feeding', 'transport', 'treatment', 'inspection', 'birth', 'other'])->default('grazing');
            $table->timestamp('entered_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['livestock_id', 'entered_at']);
            $table->index(['field_id', 'entered_at']);
            $table->index(['farm_id', 'entered_at']);
            $table->index('entered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestock_locations');
    }
};
