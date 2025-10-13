<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Student;
use Carbon\Carbon;

class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    public function definition(): array
    {
        $quizId = Quiz::inRandomOrder()->first()->id ?? Quiz::factory()->create()->id;
        $studentId = Student::inRandomOrder()->first()->id ?? Student::factory()->create()->id;

        $startedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $isCompleted = $this->faker->boolean(70);
        $totalQuestions = $this->faker->numberBetween(5, 20);

        if ($isCompleted) {
            $correctAnswers = $this->faker->numberBetween(0, $totalQuestions);
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
            $timeTaken = $this->faker->numberBetween(30, 1800);
            $completedAt = Carbon::instance($startedAt)->addMinutes($this->faker->numberBetween(5, 120));
            $status = 'completed';
        } else {
            $correctAnswers = 0;
            $score = 0;
            $timeTaken = null;
            $completedAt = null;
            $status = $this->faker->randomElement(['in_progress', 'expired']);
        }

        return [
            'quiz_id' => $quizId,
            'student_id' => $studentId,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'time_taken' => $timeTaken,
            'status' => $status,
            'created_at' => $startedAt,
            'updated_at' => $startedAt,
        ];
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $totalQuestions = $attributes['total_questions'] ?? $this->faker->numberBetween(5, 20);
            $correctAnswers = $this->faker->numberBetween(0, $totalQuestions);
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

            return [
                'status' => 'completed',
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'completed_at' => Carbon::now(),
                'time_taken' => $this->faker->numberBetween(30, 1800),
            ];
        });
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_progress',
                'score' => 0,
                'correct_answers' => 0,
                'completed_at' => null,
                'time_taken' => null,
            ];
        });
    }

    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'expired',
                'score' => 0,
                'correct_answers' => 0,
                'completed_at' => null,
                'time_taken' => null,
            ];
        });
    }

    public function forQuiz(Quiz $quiz): static
    {
        return $this->state(function (array $attributes) use ($quiz) {
            return [
                'quiz_id' => $quiz->id,
                'total_questions' => $quiz->questions_count,
            ];
        });
    }

    public function forStudent(Student $student): static
    {
        return $this->state(function (array $attributes) use ($student) {
            return [
                'student_id' => $student->id,
            ];
        });
    }
}
