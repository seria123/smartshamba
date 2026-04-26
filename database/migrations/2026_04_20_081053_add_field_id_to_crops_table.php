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
        Schema::table('crops', function (Blueprint $table) {
            $table->foreignId('field_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable()->change();
            $table->string('season_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropForeign(['field_id']);
            $table->dropColumn('field_id');
            $table->string('category')->nullable(false)->change();
            $table->string('season_type')->nullable(false)->change();
        });
    }
};
