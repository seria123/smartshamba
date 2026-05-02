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
        Schema::table('farms', function (Blueprint $table) {
            $table->enum('farm_type', ['crop', 'livestock', 'mixed'])->default('crop')->after('location');
            $table->enum('ownership_type', ['owned', 'leased', 'community'])->default('owned')->after('farm_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn(['farm_type', 'ownership_type']);
        });
    }
};
