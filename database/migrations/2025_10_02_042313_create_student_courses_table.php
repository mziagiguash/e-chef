<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();

            // Связь со студентами
            $table->foreignId('student_id')
                  ->constrained('students') // явно указываем таблицу students
                  ->onDelete('cascade');

            // Связь с курсами
            $table->foreignId('course_id')
                  ->constrained('courses') // явно указываем таблицу courses
                  ->onDelete('cascade');

            // Дополнительные поля, которые могут быть полезны
            $table->timestamp('purchased_at')->useCurrent();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');

            // Прогресс прохождения курса
            $table->integer('progress')->default(0); // в процентах
            $table->timestamp('last_accessed_at')->nullable();

            $table->timestamps();

            // Уникальный индекс чтобы студент не мог купить один курс дважды
            $table->unique(['student_id', 'course_id']);

            // Индексы для оптимизации запросов
            $table->index('student_id');
            $table->index('course_id');
            $table->index('status');
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_courses');
    }
};
