<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chemical_types', function (Blueprint $table) {
            $table->string('form')->nullable()->after('category');
            $table->string('dosage_per_m3')->nullable()->after('form');
            $table->string('dosage_per_hectare')->nullable()->after('dosage_per_m3');
            $table->text('manufacturer_instructions')->nullable()->after('dosage_per_hectare');
            $table->enum('toxicity_level', ['low', 'medium', 'high', 'critical'])->default('medium')->after('manufacturer_instructions');
            $table->string('target_pest')->nullable()->after('toxicity_level');
            $table->text('restricted')->nullable()->after('target_pest');
        });
    }

    public function down(): void
    {
        Schema::table('chemical_types', function (Blueprint $table) {
            $table->dropColumn([
                'form','dosage_per_m3','dosage_per_hectare','manufacturer_instructions',
                'toxicity_level','target_pest','restricted',
            ]);
        });
    }
};
