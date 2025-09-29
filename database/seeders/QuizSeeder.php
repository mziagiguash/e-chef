<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\QuizTranslation;
use App\Models\Lesson;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем существующие уроки
        $lessons = Lesson::with('translations')->get();

        if ($lessons->isEmpty()) {
            $this->command->info('No lessons found. Please seed lessons first.');
            return;
        }

        $quizCount = 0;

        foreach ($lessons as $lesson) {
            if (rand(1, 100) <= 30) {
                // Генерируем уникальный quiz_id
                $quizId = 'quiz_' . Str::random(10) . '_' . time();

                $quiz = Quiz::create([
                    'lesson_id' => $lesson->id,
                    'quiz_id' => $quizId, // Обязательное поле
                    'is_active' => true,
                    'time_limit' => rand(15, 45),
                    'passing_score' => rand(60, 80),
                    'max_attempts' => rand(1, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Создаем переводы для квиза
                $this->createQuizTranslations($quiz, $lesson);

                // Обновляем lesson с quiz_id (если такое поле существует)
                if (Schema::hasColumn('lessons', 'quiz_id')) {
                    $lesson->update(['quiz_id' => $quiz->id]);
                }

                $quizCount++;
            }
        }

        $this->command->info("✅ Created {$quizCount} quizzes successfully!");
        $this->command->info("📊 For {$lessons->count()} lessons");
    }

    private function createQuizTranslations(Quiz $quiz, Lesson $lesson): void
    {
        $lessonTranslations = [];
        foreach ($lesson->translations as $translation) {
            $lessonTranslations[$translation->locale] = $translation;
        }

        $translations = [
            'en' => [
                'title' => "Quiz: " . ($lessonTranslations['en']->title ?? 'Lesson ' . $lesson->id),
                'description' => "Test your knowledge of this lesson with this comprehensive quiz."
            ],
            'ru' => [
                'title' => "Тест: " . ($lessonTranslations['ru']->title ?? 'Урок ' . $lesson->id),
                'description' => "Проверьте свои знания этого урока с помощью этого комплексного теста."
            ],
            'ka' => [
                'title' => "ქვიზი: " . ($lessonTranslations['ka']->title ?? 'გაკვეთილი ' . $lesson->id),
                'description' => "გამოცადეთ თქვენი ცოდნა ამ გაკვეთილის შესახებ ამ ყოვლისმომცველი ქვიზით."
            ]
        ];

        foreach ($translations as $locale => $data) {
            if (!isset($lessonTranslations[$locale])) {
                $this->command->warn("No {$locale} translation found for lesson ID: {$lesson->id}");
                continue;
            }

            QuizTranslation::create([
                'quiz_id' => $quiz->id,
                'locale' => $locale,
                'title' => $data['title'],
                'description' => $data['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
