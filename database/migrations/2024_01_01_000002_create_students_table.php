<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students_all', function (Blueprint $table) {
            $table->id();
            // Identity
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('student_id')->unique();
            $table->date('birthdate')->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('civil_status', 20)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();

            // Academic
            $table->string('course');
            $table->string('year_level', 30);
            $table->string('section', 10)->nullable();
            $table->string('academic_year', 20)->default('2023-2024');
            $table->string('semester', 20)->default('1st');
            $table->string('enrollment_type', 30)->default('Regular');
            $table->decimal('gwa', 4, 2)->nullable();
            $table->string('status', 30)->default('Active');

            // Guardian
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship', 50)->nullable();
            $table->string('guardian_contact', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
