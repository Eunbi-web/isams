<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('student_number', 50)->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('course')->nullable();
            $table->string('year_level', 30)->nullable();
            $table->string('scholarship_name');
            $table->string('scholarship_type', 20)->default('Internal');
            $table->string('status', 30)->default('Active');
            // Performance monitoring fields
            $table->decimal('current_gwa', 4, 2)->nullable();
            $table->string('enrollment_status', 30)->default('Enrolled');
            $table->boolean('requirements_met')->default(true);
            $table->string('graduation_status', 30)->default('On Track');
            $table->text('remarks')->nullable();
            $table->string('source', 30)->default('Manual');
            $table->timestamp('last_monitored_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('scholars');
    }
};
