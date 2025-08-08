<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CourseCategory_id');
            $table->string('locale');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            
            $table->timestamps();

            $table->foreign('CourseCategory_id')->references('id')->on(''.Str::snake($model).'s'')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_category_translations');
    }
};