<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Добавляем поля для переводов
            $table->json('title')->nullable()->after('id');
            $table->json('description')->nullable()->after('title');
            $table->json('prerequisites')->nullable()->after('description');
            $table->json('keywords')->nullable()->after('prerequisites');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'prerequisites', 'keywords']);
        });
    }
};
