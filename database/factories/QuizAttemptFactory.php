<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\User;

class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    public function definition(): array
    {
        // Используем существующих пользователей (студентов с role_id = 4)
        $userId = User::where('role_id', 4)->inRandomOrder()->first()->id ??
                 User::factory()->student()->create()->id;

        $quizId = Quiz::inRandomOrder()->first()->id ??
                 Quiz::factory()->create()->id;

        $startedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $isCompleted = $this->faker->boolean(70);
        $totalQuestions = $this->faker->numberBetween(5, 20);

        return [
            'quiz_id' => $quizId,
            'user_id' => $userId,
            'score' => $isCompleted ? $this->faker->numberBetween(0, 100) : 0,
            'total_questions' => $totalQuestions,
            'correct_answers' => $isCompleted ? $this->faker->numberBetween(0, $totalQuestions) : 0,
            'started_at' => $startedAt,
            'completed_at' => $isCompleted ? $this->faker->dateTimeBetween($startedAt, 'now') : null,
            'time_taken' => $isCompleted ? $this->faker->numberBetween(30, 1800) : null,
            'status' => $isCompleted ? QuizAttempt::STATUS_COMPLETED :
                        ($this->faker->boolean(20) ? QuizAttempt::STATUS_EXPIRED : QuizAttempt::STATUS_IN_PROGRESS),
            'created_at' => $startedAt,
            'updated_at' => $startedAt,
        ];
    }
}
