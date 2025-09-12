<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('questions_translations')) {
            Schema::create('questions_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('question_id');
                $table->string('locale', 10);
                $table->text('content'); 
                $table->timestamps();

                $table->unique(['question_id', 'locale']);
                $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('questions_translations');
    }
};
