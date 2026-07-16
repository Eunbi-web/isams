<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type')->default('system');
            $table->string('title');
            $table->text('message')->nullable();
            $table->boolean('read')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users_all')->onDelete('cascade');
            $table->index(['user_id','read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
