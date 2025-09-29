<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id')->index();
            $table->enum('type', ['single', 'multiple', 'text', 'rating'])->default('single');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->integer('max_choices')->nullable()->comment('For multiple choice questions');
            $table->integer('min_rating')->default(1)->comment('For rating questions');
            $table->integer('max_rating')->default(5)->comment('For rating questions');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['quiz_id', 'order']);
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
