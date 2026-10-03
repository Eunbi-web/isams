<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('counseling_settings', function (Blueprint $table) {
            $table->id();
            // Global scheduling configuration maintained by the Guidance Counselor
            $table->foreignId('counselor_id')->nullable()->constrained('users_all')->nullOnDelete();
            $table->string('scheduling_mode', 20)->default('queue'); // queue | slots
            $table->smallInteger('slot_capacity')->default(2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('counseling_settings');
    }
};
