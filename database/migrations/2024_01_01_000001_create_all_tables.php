<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('users_all', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 20)->default('student');
            $table->string('department')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            // FIX: constrained('users_all') — not constrained() which defaults to 'users'
            $table->foreignId('user_id')->nullable()->constrained('users_all')->nullOnDelete();
            $table->string('email');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20)->default('success');
            $table->string('role', 20)->nullable();
            $table->timestamp('logged_in_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // FIX: constrained('users_all')
            $table->foreignId('user_id')->nullable()->constrained('users_all')->nullOnDelete();
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
            $table->string('course');
            $table->string('year_level', 30);
            $table->string('section', 10)->nullable();
            $table->string('academic_year', 20)->default('2023-2024');
            $table->string('semester', 20)->default('2nd');
            $table->string('enrollment_type', 30)->default('Regular');
            $table->string('income_bracket', 30)->default('below_200');
            $table->decimal('gwa', 4, 2)->nullable();
            $table->string('status', 30)->default('Active');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship', 50)->nullable();
            $table->string('guardian_contact', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 50);
            $table->text('description')->nullable();
            $table->text('benefits')->nullable();
            $table->text('requirements')->nullable();
            $table->integer('slots')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('source', 200)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 30)->default('Active');
            $table->json('ai_criteria')->nullable();
            $table->timestamps();
        });

        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->decimal('gwa', 4, 2);
            $table->string('enrollment_type', 30)->default('Regular');
            $table->boolean('has_failing')->default(false);
            $table->boolean('has_discipline')->default(false);
            $table->string('income_bracket', 30)->default('below_200');
            $table->text('essay')->nullable();
            $table->text('remarks')->nullable();
            $table->json('docs_submitted')->nullable();
            $table->string('status', 30)->default('Pending');
            $table->smallInteger('ai_score')->default(0);
            $table->string('ai_eligibility', 30)->default('Pending');
            $table->string('ai_tag', 50)->nullable();
            $table->text('ai_reasoning')->nullable();
            $table->timestamp('ai_run_at')->nullable();
            $table->timestamps();
        });

        Schema::create('scholarship_grantees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('Active');
            $table->decimal('gwa_at_award', 4, 2)->nullable();
            $table->date('awarded_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['scholarship_id', 'student_id']);
        });

        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            // FIX: constrained('users_all') — not constrained('users')
            $table->foreignId('counselor_id')->nullable()->constrained('users_all')->nullOnDelete();
            $table->string('concern_type', 100);
            $table->text('concern_detail')->nullable();
            $table->string('priority', 20)->default('Normal');
            $table->string('preferred_time', 50)->nullable();
            $table->date('preferred_date')->nullable();
            $table->date('session_date')->nullable();
            $table->string('session_time', 50)->nullable();
            $table->string('venue', 200)->nullable();
            $table->integer('queue_position')->nullable();
            $table->string('status', 30)->default('In Queue');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements_all', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        

        
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('scraped_scholarships');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('counseling_sessions');
        Schema::dropIfExists('scholarship_grantees');
        Schema::dropIfExists('scholarship_applications');
        Schema::dropIfExists('scholarships');
        Schema::dropIfExists('students');
        Schema::dropIfExists('login_logs');
        Schema::dropIfExists('users_all');
    }
};