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
        Schema::create('lessons', function (Blueprint $table) {
    $table->id();
    $table->text('title')->nullable(); // изменено на text
    $table->unsignedBigInteger('course_id');
    $table->unsignedBigInteger('quiz_id')->nullable();
    $table->text('description')->nullable(); // изменено на text
    $table->text('notes')->nullable(); // изменено на text
    $table->timestamps();
    $table->softDeletes();

    $table->index('course_id');
    $table->index('quiz_id');

    $table->foreign('course_id')
          ->references('id')
          ->on('courses')
          ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
