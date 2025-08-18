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
            $table->string('locale', 5);
            $table->string('name');
            $table->text('bio')->nullable();
            $table->text('designation')->nullable();
            $table->timestamps();

            // Правильный способ указать внешний ключ
            $table->foreign('instructor_id')
                  ->references('id')
                  ->on('instructors') // имя основной таблицы
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_translations');
    }
};
