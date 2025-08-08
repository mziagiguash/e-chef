<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('option_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('option_id')->constrained()->onDelete('cascade');
    $table->string('locale', 5);
    $table->text('option_text');
    $table->timestamps();

    $table->unique(['option_id', 'locale']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('option_translations');
    }
};
