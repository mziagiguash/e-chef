<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->string('locale', 2); // en, ru, ka
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('title')->nullable();
            $table->string('designation')->nullable();
            $table->timestamps();

            // Уникальный индекс для предотвращения дубликатов
            $table->unique(['instructor_id', 'locale']);

            // Внешний ключ
            $table->foreign('instructor_id')
                  ->references('id')
                  ->on('instructors')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_translations');
    }
};
