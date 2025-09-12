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
    Schema::table('lessons', function (Blueprint $table) {
        $table->foreign('quiz_id')
              ->references('id')
              ->on('quizzes')
              ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('lessons', function (Blueprint $table) {
        $table->dropForeign(['quiz_id']);
    });
}
};
