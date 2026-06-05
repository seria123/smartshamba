<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('email')->nullable()->after('last_name');
            $table->string('profile_photo')->nullable()->after('email');
            $table->string('employee_id')->nullable()->after('national_id');
            $table->string('employment_type')->default('permanent')->after('payment_type');
            $table->string('current_field_id')->nullable()->after('farm_id');

            $table->index('employee_id');
            $table->index('email');
            $table->index('current_field_id');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'profile_photo',
                'employee_id',
                'employment_type',
                'current_field_id',
            ]);
        });
    }
};
