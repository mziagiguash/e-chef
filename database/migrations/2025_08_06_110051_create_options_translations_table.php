<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('options_translations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('option_id')->index();
    $table->string('locale', 10);
    $table->string('option_text');
    $table->timestamps();

    $table->unique(['option_id', 'locale']);
    $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('option_translations');
    }
};
