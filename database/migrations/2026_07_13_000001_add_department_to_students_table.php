<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'department')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('department', 100)->nullable()->after('course');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'department')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }
};
