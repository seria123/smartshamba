<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_seasons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('season_type')->default('main');
            $table->string('status')->default('planned');
            $table->timestamps();

            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_seasons');
    }
};
