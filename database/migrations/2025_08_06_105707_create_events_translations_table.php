<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('events_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('locale', 10);
            $table->string('title');
            $table->text('description');
            $table->text('topic')->nullable();
            $table->text('goal')->nullable();
            $table->string('location')->nullable();
            $table->string('hosted_by')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'locale']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events_translations');
    }
}
