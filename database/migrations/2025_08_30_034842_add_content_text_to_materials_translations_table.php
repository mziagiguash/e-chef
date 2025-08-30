<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentTextToMaterialsTranslationsTable extends Migration
{
    public function up()
    {
        Schema::table('materials_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('materials_translations', 'content_text')) {
                $table->text('content_text')->nullable()->after('content');
            }
        });
    }

    public function down()
    {
        Schema::table('materials_translations', function (Blueprint $table) {
            if (Schema::hasColumn('materials_translations', 'content_text')) {
                $table->dropColumn('content_text');
            }
        });
    }
}
