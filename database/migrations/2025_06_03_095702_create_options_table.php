<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('key', 1)->comment('a, b, c, d etc.');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['question_id', 'key']);
            $table->index(['question_id', 'is_correct']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
};
