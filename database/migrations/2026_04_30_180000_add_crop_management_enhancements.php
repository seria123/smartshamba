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
        Schema::table('crop_cycles', function (Blueprint $table) {
            $table->string('crop_name')->nullable()->after('field_id');
            $table->string('category')->nullable()->after('crop_name');
            $table->string('variety')->nullable()->after('category');
            $table->string('season')->nullable()->after('variety');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('set null')->after('season');
            $table->string('irrigation_type')->nullable()->after('start_date');
            $table->text('irrigation_schedule')->nullable()->after('irrigation_type');
            $table->text('drainage')->nullable()->after('irrigation_schedule');
            $table->decimal('ph_level', 4, 2)->nullable()->after('drainage');
            $table->foreignId('previous_crop_cycle_id')->nullable()->after('ph_level')
                ->constrained('crop_cycles')->onDelete('set null');
            $table->string('soil_type_override')->nullable()->after('previous_crop_cycle_id');
            $table->string('water_source_override')->nullable()->after('soil_type_override');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('labor_type')->nullable()->after('cost');
           $table->unsignedBigInteger('crop_stage_id')->nullable()->change();

// Then separately manage foreign key
$table->dropForeign(['crop_stage_id']);
$table->foreign('crop_stage_id')
      ->references('id')
      ->on('crop_stages')
      ->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->onDelete('cascade');
        });

        Schema::table('crop_stages', function (Blueprint $table) {
            $table->enum('health_status', ['excellent', 'good', 'fair', 'poor'])->nullable()->after('end_date');
            $table->decimal('germination_rate', 5, 2)->nullable()->after('health_status');
        });

        Schema::table('weather_data', function (Blueprint $table) {
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->onDelete('cascade');
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->onDelete('set null');
        });

        Schema::table('harvests', function (Blueprint $table) {
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->onDelete('set null');
        });

        Schema::table('inputs', function (Blueprint $table) {
          // Modify column only
$table->unsignedBigInteger('activity_id')->nullable()->change();

// Then handle foreign key separately
$table->dropForeign(['activity_id']); // optional (if exists)

$table->foreign('activity_id')
      ->references('id')
      ->on('activities')
      ->cascadeOnDelete();
            $table->string('unit')->nullable()->after('quantity');
            $table->decimal('cost', 10, 2)->nullable()->after('unit');
            $table->date('application_date')->nullable()->after('cost');
            $table->string('application_method')->nullable()->after('application_date');
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->onDelete('cascade');
        });

        Schema::create('pest_disease_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_cycle_id')->constrained()->cascadeOnDelete();
            $table->enum('issue_type', ['pest', 'disease', 'other'])->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->string('affected_area')->nullable();
            $table->date('treatment_date')->nullable();
            $table->string('treatment_method')->nullable();
            $table->text('chemicals_used')->nullable();
            $table->enum('outcome', ['resolved', 'ongoing', 'unresolved'])->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['crop_cycle_id', 'treatment_date']);
        });

        Schema::create('growth_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_stage_id')->constrained()->cascadeOnDelete();
            $table->date('measurement_date')->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->integer('leaf_count')->nullable();
            $table->integer('fruit_count')->nullable();
            $table->text('health_notes')->nullable();
            $table->timestamps();

            $table->index(['crop_stage_id', 'measurement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_cycles', function (Blueprint $table) {
            $table->dropColumn([
                'crop_name', 'category', 'variety', 'season', 'farm_id',
                'irrigation_type', 'irrigation_schedule', 'drainage', 'ph_level', 'previous_crop_cycle_id',
                'soil_type_override', 'water_source_override'
            ]);
            $table->dropForeign(['farm_id']);
            $table->dropForeign(['previous_crop_cycle_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropForeign(['crop_cycle_id']);
            $table->dropColumn(['staff_id', 'labor_type', 'crop_cycle_id']);
            $table->foreignId('crop_stage_id')->nullable()->constrained()->cascadeOnDelete()->change();
        });

        Schema::table('crop_stages', function (Blueprint $table) {
            $table->dropColumn(['health_status', 'germination_rate']);
        });

        Schema::table('weather_data', function (Blueprint $table) {
            $table->dropForeign(['crop_cycle_id']);
            $table->dropColumn(['crop_cycle_id']);
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->dropForeign(['crop_cycle_id']);
            $table->dropColumn(['crop_cycle_id']);
        });

        Schema::table('harvests', function (Blueprint $table) {
            $table->dropForeign(['crop_cycle_id']);
            $table->dropColumn(['crop_cycle_id']);
        });

        Schema::table('inputs', function (Blueprint $table) {
           $table->dropForeign(['activity_id']);
            $table->dropColumn(['unit', 'cost', 'application_date', 'application_method', 'crop_cycle_id']);
        });

        Schema::dropIfExists('pest_disease_treatments');
        Schema::dropIfExists('growth_measurements');
    }
};
