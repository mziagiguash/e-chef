<?php
// database/migrations/2024_01_15_create_contact_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            // Для авторизованных пользователей (студентов или инструкторов)
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->enum('sender_type', ['student', 'instructor'])->nullable();

            // Обязательные поля формы
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');

            // Статус обращения
            $table->enum('status', ['new', 'in_progress', 'resolved'])->default('new');

            // Для админ-панели
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('assigned_admin_id')->nullable(); // Админ, который обрабатывает обращение
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Индексы для быстрого поиска
            $table->index(['status', 'created_at']);
            $table->index('email');
            $table->index(['sender_id', 'sender_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_messages');
    }
};
