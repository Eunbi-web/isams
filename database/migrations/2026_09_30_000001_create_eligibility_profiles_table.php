<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('eligibility_profiles')) return;

        Schema::create('eligibility_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('gwa', 4, 2)->nullable();
            $table->string('year_level', 30)->nullable();
            $table->string('enrollment_type', 30)->default('Regular');
            $table->string('income_bracket', 30)->default('below_200');
            $table->string('academic_honors', 50)->default('None');
            $table->boolean('has_failing')->default(false);
            $table->boolean('has_discipline')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligibility_profiles');
    }
};
