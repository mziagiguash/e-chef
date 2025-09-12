<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('locale', 10); // en, ru, ka
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('prerequisites')->nullable();
            $table->string('keywords')->nullable();

            $table->unique(['course_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_translations');
    }
};
