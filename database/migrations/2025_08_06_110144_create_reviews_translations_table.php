<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('locale', 10);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['review_id', 'locale']);
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews_translations');
    }
}
