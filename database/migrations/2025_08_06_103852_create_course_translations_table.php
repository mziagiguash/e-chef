<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_translations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('course_id');
    $table->string('locale', 5);
    $table->string('title');
    $table->text('description')->nullable();
    $table->timestamps();

    $table->foreign('course_id')
          ->references('id')
          ->on('courses')
          ->onDelete('cascade');

    $table->unique(['course_id', 'locale']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_translations');
    }
};
