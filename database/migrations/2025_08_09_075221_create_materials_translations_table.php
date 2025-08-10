<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('materials_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->string('locale', 10);
            $table->string('title');
            $table->string('content')->nullable();
            $table->timestamps();

            $table->unique(['material_id', 'locale']);
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('materials_translations');
    }
}
