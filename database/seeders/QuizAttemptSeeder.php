<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Student;
use Carbon\Carbon;

class QuizAttemptSeeder extends Seeder
{
    public function run()
    {
        $quizzes = Quiz::all();
        $students = Student::all();

        if ($students->isEmpty()) {
            echo "❌ No students found. Please run StudentSeeder first.\n";
            return;
        }

        if ($quizzes->isEmpty()) {
            echo "❌ No quizzes found. Please run QuizSeeder first.\n";
            return;
        }

        echo "🎯 Creating quiz attempts for {$quizzes->count()} quizzes and {$students->count()} students...\n";

        $attempts = [];
        $createdCount = 0;

        foreach ($quizzes as $quiz) {
            // Берем случайных студентов (30% от общего числа)
            $selectedStudents = $students->random(max(1, ceil($students->count() * 0.3)));

            foreach ($selectedStudents as $student) {
                // Создаем 1-2 попытки для каждого студента на квиз
                $attemptsCount = rand(1, 2);

                for ($i = 0; $i < $attemptsCount; $i++) {
                    $attempts[] = $this->createQuizAttemptData($quiz, $student, $i);
                    $createdCount++;

                    // Вставляем пачками по 100 записей
                    if (count($attempts) >= 100) {
                        try {
                            QuizAttempt::insert($attempts);
                            $attempts = [];
                        } catch (\Exception $e) {
                            echo "⚠️ Skipping batch due to error: " . $e->getMessage() . "\n";
                            $attempts = [];
                        }
                    }
                }
            }
        }

        // Вставляем оставшиеся записи
        if (!empty($attempts)) {
            try {
                QuizAttempt::insert($attempts);
            } catch (\Exception $e) {
                echo "⚠️ Skipping final batch due to error: " . $e->getMessage() . "\n";
            }
        }

        echo "✅ Successfully created {$createdCount} quiz attempts.\n";
    }

    private function createQuizAttemptData(Quiz $quiz, Student $student, int $attemptNumber): array
    {
        $startedAt = $this->generateStartedAt($attemptNumber);
        $isCompleted = rand(0, 100) > 20; // 80% завершены
        $totalQuestions = $quiz->questions_count;

        if ($isCompleted) {
            $correctAnswers = rand(0, $totalQuestions);
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
            $timeTaken = rand(60, 1800); // 1-30 минут в секундах
            $completedAt = $this->generateCompletedAt($startedAt);
            $status = 'completed';
        } else {
            $correctAnswers = 0;
            $score = 0;
            // Для expired/in_progress устанавливаем время 0 вместо null
            $timeTaken = 0;
            $completedAt = null;
            $status = rand(0, 1) ? 'in_progress' : 'expired';
        }

        $now = Carbon::now();

        return [
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'time_taken' => $timeTaken, // НИКОГДА не null
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function generateStartedAt(int $attemptNumber): Carbon
    {
        $daysAgo = rand(0, 60) + $attemptNumber;
        return Carbon::now()->subDays($daysAgo)->subMinutes(rand(0, 1440));
    }

    private function generateCompletedAt(Carbon $startedAt): Carbon
    {
        $minutesTaken = rand(5, 120);
        return $startedAt->copy()->addMinutes($minutesTaken);
    }
}
