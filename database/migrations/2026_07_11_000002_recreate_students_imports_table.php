<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recreate the staging table with only the required columns.
        // This is intentionally destructive to match the new import CSV contract.
        Schema::dropIfExists('students_imports');

        Schema::create('students_imports', function (Blueprint $table) {
            $table->id();

            // Allows grouping rows per CSV upload.
            $table->string('import_batch_id', 80)->index();

            $table->string('student_id')->index();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');


            $table->timestamps();

            // Prevent duplicates within a batch for same student_id.
            $table->unique(['import_batch_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students_imports');
    }
};

