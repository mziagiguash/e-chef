<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
    $table->string('locale', 5);
    $table->string('title');
    $table->timestamps();

    $table->unique(['quiz_id', 'locale']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_translations');
    }
};
