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
        Schema::table('spraying_schedules', function (Blueprint $table) {
            $table->foreignId('chemical_id')->nullable()->constrained('chemical_types')->onDelete('set null');
            $table->index('chemical_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spraying_schedules', function (Blueprint $table) {
            $table->dropForeign(['chemical_id']);
            $table->dropIndex(['chemical_id']);
            $table->dropColumn('chemical_id');
        });
    }
};
