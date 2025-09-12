<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\QuizTranslation;
use App\Models\Lesson;
use App\Models\LessonTranslation;

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

        // Создаем квизы для некоторых уроков (примерно 30% уроков получат квизы)
        foreach ($lessons as $lesson) {
            // 30% chance to create a quiz for this lesson
            if (rand(1, 100) <= 30) {
                $quiz = Quiz::create([
                    'lesson_id' => $lesson->id,
                    'is_active' => true,
                    'time_limit' => rand(15, 45),
                    'passing_score' => rand(60, 80),
                    'max_attempts' => rand(1, 3),
                    'title' => null,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Создаем переводы для квиза
                $this->createQuizTranslations($quiz, $lesson);

                // Обновляем lesson с quiz_id
                $lesson->update(['quiz_id' => $quiz->id]);

                $quizCount++;
            }
        }

        $this->command->info("✅ Created {$quizCount} quizzes successfully!");
        $this->command->info("📊 For {$lessons->count()} lessons");
    }

    private function createQuizTranslations(Quiz $quiz, Lesson $lesson): void
    {
        // Правильно получаем переводы урока
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
            // Проверяем, существует ли перевод для этого языка
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
