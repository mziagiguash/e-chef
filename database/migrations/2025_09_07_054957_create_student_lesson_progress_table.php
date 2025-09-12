<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_student_lesson_progress_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0); // 0-100%
            $table->integer('video_position')->default(0); // текущая позиция в секундах
            $table->integer('video_duration')->default(0); // длительность видео в секундах
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->useCurrent();
            $table->timestamps();

            // Уникальный индекс чтобы избежать дубликатов
            $table->unique(['student_id', 'lesson_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_lesson_progress');
    }
};
