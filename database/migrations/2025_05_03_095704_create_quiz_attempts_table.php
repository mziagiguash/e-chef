<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('quiz_attempts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
        $table->foreignId('student_id')->constrained()->onDelete('cascade'); // ← ДОЛЖНО БЫТЬ!
        $table->integer('score')->default(0);
        $table->integer('total_questions')->default(0);
        $table->integer('correct_answers')->default(0);
        $table->timestamp('started_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->integer('time_taken')->default(0)->comment('In seconds');
        $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress');
        $table->timestamps();

        $table->index(['quiz_id', 'student_id']); // ← Лучше составной индекс
        $table->index(['student_id', 'status']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
