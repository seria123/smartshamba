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
        Schema::table('irrigation_zones', function (Blueprint $table) {
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('irrigation_zones', function (Blueprint $table) {
            $table->dropColumn(['end_time', 'is_active', 'notes']);
        });
    }
};
