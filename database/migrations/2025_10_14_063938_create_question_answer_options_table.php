<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_xxxxxx_create_question_answer_options_table.php
public function up()
{
    Schema::create('question_answer_options', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('question_answer_id');
        $table->unsignedBigInteger('option_id');
        $table->timestamps();

        $table->foreign('question_answer_id')->references('id')->on('question_answers')->onDelete('cascade');
        $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answer_options');
    }
};
