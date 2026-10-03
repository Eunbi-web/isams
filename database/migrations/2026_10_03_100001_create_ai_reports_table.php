<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_reports', function (Blueprint $table) {
            $table->id();
            // Which portal generated it: 'dsa' or 'scholarship'
            $table->string('portal', 20);
            // Report scope: 'scholarship', 'counseling' or 'both'
            $table->string('scope', 20);
            $table->string('title');
            $table->string('academic_year', 20);
            $table->string('semester', 30);
            $table->string('prepared_by')->nullable();
            $table->longText('content_html');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_reports');
    }
};
