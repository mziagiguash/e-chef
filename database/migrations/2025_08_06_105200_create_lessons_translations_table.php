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
    Schema::create('lessons_translations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('lesson_id');
        $table->string('locale', 10);
        $table->string('title'); // ТОЛЬКО здесь храним заголовок
        $table->text('description')->nullable(); // ТОЛЬКО здесь
        $table->text('notes')->nullable(); // ТОЛЬКО здесь
        $table->timestamps();

        $table->unique(['lesson_id', 'locale']);

        $table->foreign('lesson_id')
              ->references('id')
              ->on('lessons')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons_translations');
    }
};
