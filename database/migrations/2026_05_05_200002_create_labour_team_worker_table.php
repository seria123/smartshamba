<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_team_worker', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('labour_team_id')->constrained('labour_teams')->cascadeOnDelete();
            $table->foreignId('labour_worker_id')->constrained('labour_workers')->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['labour_team_id', 'labour_worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labour_team_worker');
    }
};
