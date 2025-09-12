<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quizzes_translations')) {
            Schema::create('quizzes_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('quiz_id');
                $table->string('locale', 10);
                $table->string('title');
                $table->string('description');
                $table->timestamps();

                $table->unique(['quiz_id', 'locale']);
                $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes_translations');
    }
};
