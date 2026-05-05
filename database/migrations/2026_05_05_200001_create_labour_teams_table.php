<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_teams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('team_type');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['farm_id', 'name']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labour_teams');
    }
};
