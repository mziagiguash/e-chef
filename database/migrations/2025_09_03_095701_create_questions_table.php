<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id')->index();
            $table->text('content')->nullable(); // Добавлено поле для текста вопроса
            $table->enum('type', ['single', 'multiple', 'text', 'rating'])->default('single');
            $table->integer('order')->default(0);
            $table->integer('points')->default(1);
            $table->boolean('is_required')->default(true);
            $table->integer('max_choices')->nullable();
            $table->integer('min_rating')->nullable();
            $table->integer('max_rating')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
