<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_species', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('species_type');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'status']);
        });

        Schema::create('livestock_breeds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('livestock_species')->restrictOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['species_id', 'code']);
            $table->index(['organization_id', 'status']);
        });

        Schema::create('livestock_animals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->foreignId('species_id')->constrained('livestock_species')->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('livestock_breeds')->nullOnDelete();
            $table->string('animal_code');
            $table->string('tag_number')->nullable();
            $table->string('rfid_number')->nullable();
            $table->string('name')->nullable();
            $table->string('sex')->default('unknown');
            $table->date('date_of_birth')->nullable();
            $table->string('source')->default('other');
            $table->string('status')->default('active');
            $table->string('health_status')->nullable();
            $table->string('production_status')->nullable();
            $table->foreignId('dam_id')->nullable()->constrained('livestock_animals')->nullOnDelete();
            $table->foreignId('sire_id')->nullable()->constrained('livestock_animals')->nullOnDelete();
            $table->decimal('current_weight', 12, 2)->nullable();
            $table->string('weight_unit')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_cost', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['farm_id', 'animal_code']);
            $table->unique(['farm_id', 'tag_number']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });

        Schema::create('livestock_animal_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->foreignId('species_id')->constrained('livestock_species')->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('livestock_breeds')->nullOnDelete();
            $table->string('group_code');
            $table->string('name');
            $table->string('group_type')->default('other');
            $table->date('start_date')->nullable();
            $table->unsignedInteger('initial_count')->default(0);
            $table->unsignedInteger('current_count')->default(0);
            $table->string('sex_composition')->nullable();
            $table->string('source')->default('other');
            $table->string('status')->default('active');
            $table->string('health_status')->nullable();
            $table->string('production_status')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['farm_id', 'group_code']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });

        Schema::create('livestock_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('event_number')->unique();
            $table->string('event_type');
            $table->date('event_date');
            $table->string('status')->default('recorded');
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('performed_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_treatment_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('related_event_id')->nullable()->constrained('livestock_events')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('treatment_type');
            $table->date('treatment_date');
            $table->string('condition_or_reason')->nullable();
            $table->string('diagnosis')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('inventory_products')->nullOnDelete();
            $table->string('product_name_snapshot')->nullable();
            $table->decimal('dosage', 12, 2)->nullable();
            $table->string('dosage_unit')->nullable();
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('withdrawal_meat_days')->nullable();
            $table->unsignedInteger('withdrawal_milk_days')->nullable();
            $table->unsignedInteger('withdrawal_egg_days')->nullable();
            $table->foreignId('treated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('treated_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_withdrawal_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('treatment_record_id')->constrained('livestock_treatment_records')->cascadeOnDelete();
            $table->string('withdrawal_type');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_breeding_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('related_event_id')->nullable()->constrained('livestock_events')->nullOnDelete();
            $table->date('breeding_date');
            $table->string('breeding_method');
            $table->foreignId('male_animal_id')->nullable()->constrained('livestock_animals')->nullOnDelete();
            $table->string('sire_name_snapshot')->nullable();
            $table->date('expected_due_date')->nullable();
            $table->string('status')->default('recorded');
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('performed_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_pregnancy_checks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('related_breeding_record_id')->nullable()->constrained('livestock_breeding_records')->nullOnDelete();
            $table->date('check_date');
            $table->string('result');
            $table->date('expected_due_date')->nullable();
            $table->foreignId('checked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_birth_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('mother_animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('related_event_id')->nullable()->constrained('livestock_events')->nullOnDelete();
            $table->date('birth_date');
            $table->unsignedInteger('number_born');
            $table->unsignedInteger('number_alive')->default(0);
            $table->unsignedInteger('number_dead')->default(0);
            $table->string('birth_type')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_weight_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->date('weigh_date');
            $table->decimal('weight', 12, 2);
            $table->string('weight_unit');
            $table->string('measurement_method')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_feed_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->date('feed_date');
            $table->foreignId('product_id')->nullable()->constrained('inventory_products')->nullOnDelete();
            $table->string('feed_name_snapshot')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->string('quantity_unit');
            $table->string('feeding_method')->nullable();
            $table->foreignId('fed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('fed_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_movement_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->foreignId('from_paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->foreignId('to_paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->date('movement_date');
            $table->string('movement_reason')->nullable();
            $table->foreignId('moved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('moved_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_mortality_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->date('mortality_date');
            $table->unsignedInteger('number_dead')->nullable();
            $table->string('cause')->nullable();
            $table->string('suspected_reason')->nullable();
            $table->string('disposal_method')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('livestock_yield_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->cascadeOnDelete();
            $table->date('yield_date');
            $table->string('yield_type');
            $table->decimal('quantity', 12, 2);
            $table->string('unit_of_measure');
            $table->string('grade')->nullable();
            $table->string('destination')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_yield_records');
        Schema::dropIfExists('livestock_mortality_records');
        Schema::dropIfExists('livestock_movement_records');
        Schema::dropIfExists('livestock_feed_records');
        Schema::dropIfExists('livestock_weight_records');
        Schema::dropIfExists('livestock_birth_records');
        Schema::dropIfExists('livestock_pregnancy_checks');
        Schema::dropIfExists('livestock_breeding_records');
        Schema::dropIfExists('livestock_withdrawal_periods');
        Schema::dropIfExists('livestock_treatment_records');
        Schema::dropIfExists('livestock_events');
        Schema::dropIfExists('livestock_animal_groups');
        Schema::dropIfExists('livestock_animals');
        Schema::dropIfExists('livestock_breeds');
        Schema::dropIfExists('livestock_species');
    }
};
