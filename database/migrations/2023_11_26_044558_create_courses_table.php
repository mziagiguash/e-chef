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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_category_id')->constrained();
            $table->foreignId('instructor_id')->constrained();
            $table->enum('courseType', ['free', 'paid', 'subscription']);
            $table->decimal('coursePrice', 10, 2)->default(0);
            $table->decimal('courseOldPrice', 10, 2)->nullable();
            $table->decimal('subscription_price', 10, 2)->nullable();
            $table->date('start_from');
            $table->integer('duration');
            $table->integer('lesson');
            $table->string('course_code');
            $table->string('thumbnail_video_url')->nullable();
            $table->enum('tag', ['popular', 'featured', 'upcoming'])->nullable();
            $table->boolean('status')->default(true)->comment('1 active, 0 inactive');
            $table->string('image');
            $table->string('thumbnail_image')->nullable();
            $table->string('thumbnail_video_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
