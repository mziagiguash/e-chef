<?php
// database/migrations/2024_01_15_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Для студентов
            $table->unsignedBigInteger('student_id')->nullable();

            // Для инструкторов
            $table->unsignedBigInteger('instructor_id')->nullable();

            // Основные поля
            $table->string('type'); // contact_message_replied, course_update, system, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Дополнительные данные

            // Ссылка на контактное сообщение (если применимо)
            $table->unsignedBigInteger('contact_message_id')->nullable();

            // Статус прочтения
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index(['student_id', 'is_read']);
            $table->index(['instructor_id', 'is_read']);
            $table->index(['type', 'created_at']);

            // Внешние ключи
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            $table->foreign('contact_message_id')->references('id')->on('contact_messages')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
