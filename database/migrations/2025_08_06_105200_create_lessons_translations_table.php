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
            $table->id(); // bigint(20) unsigned NOT NULL AUTO_INCREMENT
            $table->unsignedBigInteger('lesson_id'); // bigint(20) unsigned NOT NULL
            $table->string('locale', 10); // varchar(10) NOT NULL
            $table->string('title'); // varchar(255) NOT NULL
            $table->text('description')->nullable(); // text DEFAULT NULL
            $table->text('notes')->nullable(); // text DEFAULT NULL
            $table->timestamps(); // created_at и updated_at

            // Уникальный ключ
            $table->unique(['lesson_id', 'locale']); // UNIQUE KEY `lessons_translations_lesson_id_locale_unique`

            // Внешний ключ
            $table->foreign('lesson_id')
                  ->references('id')
                  ->on('lessons')
                  ->onDelete('cascade'); // CONSTRAINT `lessons_translations_lesson_id_foreign`
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
