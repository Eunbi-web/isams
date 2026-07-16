<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        

        // ── Discipline Cases ─────────────────────────────────────────────────
        Schema::create('discipline_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 50)->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('violation_type', 100);
            $table->date('incident_date');
            $table->text('description')->nullable();
            $table->text('witnesses')->nullable();
            $table->string('status', 50)->default('Under Investigation');
            $table->date('hearing_date')->nullable();
            $table->string('sanction', 200)->nullable();
            $table->text('remarks')->nullable();
            $table->string('filed_by')->nullable();
            $table->timestamps();
        });

        // ── Organizations ────────────────────────────────────────────────────
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('acronym', 20)->nullable();
            $table->string('type', 50); // Academic, Cultural, Sports, Religious, Service, Political
            $table->text('description')->nullable();
            $table->string('adviser')->nullable();
            $table->string('president')->nullable();
            $table->unsignedSmallInteger('year_founded')->nullable();
            $table->string('status', 30)->default('Active');
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50)->default('Member');
            $table->date('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'student_id']);
        });

        // ── Scholarships ─────────────────────────────────────────────────────
        

        

        // ── Events ───────────────────────────────────────────────────────────
        Schema::create('events_all', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 50); // Leadership, Academic, Cultural, Sports, Wellness, Service, Social
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->date('end_date')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->string('venue', 200)->nullable();
            $table->string('organizer', 200)->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedInteger('actual_participants')->default(0);
            $table->string('status', 30)->default('Upcoming'); // Upcoming, Ongoing, Completed, Cancelled
            $table->string('banner')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('scholarship_grantees');
        Schema::dropIfExists('scholarships');
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('discipline_cases');
        Schema::dropIfExists('counseling_sessions');
    }
};
