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
        Schema::table('crop_analyses', function (Blueprint $table) {
            $table->foreignId('crop_cycle_id')
                ->nullable()
                ->constrained('crop_cycles')
                ->onDelete('set null')
                ->after('field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_analyses', function (Blueprint $table) {
            $table->dropForeign(['crop_cycle_id']);
            $table->dropColumn('crop_cycle_id');
        });
    }
};
