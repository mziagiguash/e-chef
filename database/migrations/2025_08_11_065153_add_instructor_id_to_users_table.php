<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('instructor_id')->nullable()->after('id');
        // Если нужна связь с таблицей instructors, добавьте foreign key:
        // $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // если добавляли foreign key, удалите сначала её:
        // $table->dropForeign(['instructor_id']);
        $table->dropColumn('instructor_id');
    });
}

};
