<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            $table->boolean('follow_up_required')->default(false)->after('notes');
            $table->date('follow_up_date')->nullable()->after('follow_up_required');
        });
    }

    public function down(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            $table->dropColumn(['follow_up_required', 'follow_up_date']);
        });
    }
};
