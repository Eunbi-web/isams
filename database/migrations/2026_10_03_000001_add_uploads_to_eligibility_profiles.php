<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eligibility_profiles', function (Blueprint $table) {
            // Uploaded documents are stored as compressed base64 data URLs so
            // they survive on Vercel (serverless filesystem is ephemeral).
            $table->text('school_id_photo')->nullable();
            $table->text('coe_file')->nullable();
            $table->decimal('family_income', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('eligibility_profiles', function (Blueprint $table) {
            $table->dropColumn(['school_id_photo', 'coe_file', 'family_income']);
        });
    }
};
