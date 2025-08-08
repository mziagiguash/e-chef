<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'locale']); // чтобы не было дублирующих переводов
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_translations');
    }
};
