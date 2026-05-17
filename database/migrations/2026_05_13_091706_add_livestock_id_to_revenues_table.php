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
        Schema::table('revenues', function (Blueprint $table) {
            if (!Schema::hasColumn('revenues', 'livestock_id')) {
                $table->foreignId('livestock_id')->nullable()->after('id');
            }
            $table->foreign('livestock_id')
                  ->references('id')
                  ->on('livestock')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenues', function (Blueprint $table) {
            //
        });
    }
};
