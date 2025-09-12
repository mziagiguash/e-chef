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
                $table->unsignedBigInteger('lesson_id')->nullable()->index();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('time_limit')->nullable()->comment('Time limit in minutes');
                $table->integer('passing_score')->default(70);
                $table->integer('max_attempts')->default(1); 
                $table->string('title')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
