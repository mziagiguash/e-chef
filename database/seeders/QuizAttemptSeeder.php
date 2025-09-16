<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizAttemptSeeder extends Seeder
{
    public function run()
    {
        $quizzes = Quiz::all();

        // Получаем всех студентов (role_id = 4)
        $students = User::where('role_id', 4)->get();

        if ($students->isEmpty()) {
            $this->command->info('No students found! Please run UserSeeder first.');
            return;
        }

        if ($quizzes->isEmpty()) {
            $this->command->info('No quizzes found! Please run QuizSeeder first.');
            return;
        }

        foreach ($students as $student) {
            foreach ($quizzes as $quiz) {
                $attemptCount = rand(1, 3); // 1-3 попытки на квиз

                for ($i = 0; $i < $attemptCount; $i++) {
                    $totalQuestions = $quiz->questions()->count();

                    // Если нет вопросов, пропускаем
                    if ($totalQuestions === 0) {
                        continue;
                    }

                    $correctAnswers = rand(0, $totalQuestions);
                    $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

                    QuizAttempt::create([
                        'quiz_id' => $quiz->id,
                        'user_id' => $student->id,
                        'score' => $score,
                        'total_questions' => $totalQuestions,
                        'correct_answers' => $correctAnswers,
                        'started_at' => fake()->dateTimeBetween('-1 month', 'now'),
                        'completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
                        'time_taken' => rand(60, 1800),
                        'status' => 'completed',
                    ]);
                }
            }
        }

        $this->command->info('Quiz attempts seeded successfully!');
    }
}
