<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            $table->string('year_level')->nullable()->after('enrollment_type');
            $table->string('academic_load')->nullable()->after('year_level');
            $table->string('application_type')->nullable()->after('academic_load');
            $table->string('academic_honors')->nullable()->after('application_type');
            $table->string('parent_employment_status')->nullable()->after('academic_honors');
            $table->unsignedTinyInteger('siblings_in_college')->default(0)->after('parent_employment_status');
        });
    }

    public function down(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            $table->dropColumn(['year_level', 'academic_load', 'application_type', 'academic_honors', 'parent_employment_status', 'siblings_in_college']);
        });
    }
};
