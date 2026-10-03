<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scholarship_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users_all')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            // Who issued the report the update came from: Government / Private / Institutional
            $table->string('source_type', 20)->default('Government');
            $table->timestamps();
            $table->index('scholarship_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('scholarship_updates');
    }
};
