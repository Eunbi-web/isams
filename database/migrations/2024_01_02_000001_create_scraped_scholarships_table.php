<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('scraped_scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('benefits')->nullable();
            $table->text('requirements')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('slots')->nullable();
            $table->boolean('is_open')->default(true);
            $table->string('source_url', 500)->nullable();
            $table->string('source_agency')->nullable();
            $table->string('source_type', 50)->nullable();
            // Status: new, updated, unchanged
            $table->string('status', 30)->default('new');
            $table->smallInteger('ai_confidence')->default(0);
            $table->boolean('imported')->default(false);
            $table->foreignId('scholarship_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('last_scraped_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('scraped_scholarships'); }
};
