<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    if (!Schema::hasTable('quizzes')) {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('lesson_id')->nullable(false)->index();

            // ДОБАВЛЯЕМ НЕОБХОДИМЫЕ ПОЛЯ
            
            $table->integer('questions_count')->default(0);

            // УБИРАЕМ ЛИШНЕЕ
            // $table->integer('order')->default(0); // ← УДАЛЯЕМ (не нужно)
            // $table->string('quiz_id')->nullable()->unique()->comment('Уникальный идентификатор квиза'); // ← УДАЛЯЕМ

            $table->boolean('is_active')->default(true);
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
            $table->integer('passing_score')->default(70);
            $table->integer('max_attempts')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->unique('lesson_id'); // один квиз на урок - обеспечивает уникальность
        });
    }
}
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
