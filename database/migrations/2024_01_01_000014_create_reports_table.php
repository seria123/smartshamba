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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->string('report_type'); // daily_summary, weekly_summary, monthly_summary, crop_analysis, irrigation_report, sensor_analysis, financial
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('report_period_start');
            $table->dateTime('report_period_end');
            $table->string('file_path')->nullable();
            $table->json('data')->nullable();
            $table->string('status')->default('pending'); // pending, generating, completed, failed
            $table->timestamps();
            
            $table->index(['farm_id', 'report_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};