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
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'instructor_id')) {
            // Удаляем колонку — Laravel сам удалит индекс и ключ, если они есть
            $table->dropColumn('instructor_id');
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('instructor_id')->nullable();

        // Добавить внешний ключ обратно, если нужно
        $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('set null');
    });
}

};
