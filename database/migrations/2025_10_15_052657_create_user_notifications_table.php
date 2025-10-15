<?php
// database/migrations/2024_01_15_create_user_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->unsignedBigInteger('contact_message_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'is_read']);
            $table->index(['instructor_id', 'is_read']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
};
