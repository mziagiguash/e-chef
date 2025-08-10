<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('courses_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('locale', 10);
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('prerequisites')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'locale']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses_translations');
    }
}
