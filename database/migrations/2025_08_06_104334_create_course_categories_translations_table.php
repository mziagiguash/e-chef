<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseCategoriesTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('course_categories_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_category_id');
            $table->string('locale', 10);
            $table->string('category_name');
            $table->timestamps();

            $table->unique(['course_category_id', 'locale']);
            $table->foreign('course_category_id')->references('id')->on('course_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_categories_translations');
    }
}
