<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Добавляем parent_id для связи с родительским сообщением
            $table->foreignId('parent_id')->nullable()->after('id')
                  ->constrained('contact_messages')->onDelete('cascade');

            // Индекс для улучшения производительности
            $table->index(['parent_id', 'sender_id']);
        });
    }

    public function down()
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
