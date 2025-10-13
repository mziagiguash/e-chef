<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Lesson;

class QuizSeeder extends Seeder
{
    public function run()
    {
        $lessons = Lesson::all();

        foreach ($lessons as $lesson) {
            // Создаем квиз для каждого урока (только один квиз на урок)
            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'title' => 'Quiz for ' . $lesson->translations->first()->title,
                'questions_count' => $this->generateQuestionsCount(),
                'time_limit' => $this->generateTimeLimit(),
                'passing_score' => $this->generatePassingScore(),
                'max_attempts' => $this->generateMaxAttempts(),
                'is_active' => true,
            ]);

            // Создаем переводы для квиза
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                $lessonTranslation = $lesson->translations->where('locale', $locale)->first();
                $lessonTitle = $lessonTranslation ? $lessonTranslation->title : $lesson->translations->first()->title;

                $quiz->translations()->create([
                    'locale' => $locale,
                    'title' => $this->generateQuizTitle($locale, $lessonTitle),
                    'description' => $this->generateQuizDescription($locale),
                ]);
            }
        }
    }

    private function generateQuestionsCount(): int
    {
        $counts = [5, 8, 10, 12, 15];
        return $counts[array_rand($counts)];
    }

    private function generateTimeLimit(): int
    {
        $limits = [10, 15, 20, 30, 45, 60];
        return $limits[array_rand($limits)];
    }

    private function generatePassingScore(): int
    {
        $scores = [60, 65, 70, 75, 80];
        return $scores[array_rand($scores)];
    }

    private function generateMaxAttempts(): int
    {
        $attempts = [1, 2, 3];
        return $attempts[array_rand($attempts)];
    }

    private function generateQuizTitle(string $locale, string $lessonTitle): string
    {
        $prefixes = match($locale) {
            'en' => ['Quiz: ', 'Test: ', 'Assessment: ', 'Knowledge Check: '],
            'ru' => ['Тест: ', 'Проверка: ', 'Оценка: ', 'Экзамен: '],
            'ka' => ['ტესტი: ', 'შემოწმება: ', 'შეფასება: ', 'გამოცდა: ']
        };

        return $prefixes[array_rand($prefixes)] . $lessonTitle;
    }

    private function generateQuizDescription(string $locale): string
    {
        return match($locale) {
            'en' => 'Complete this quiz to test your understanding of the lesson material.',
            'ru' => 'Пройдите этот тест, чтобы проверить свое понимание материала урока.',
            'ka' => 'შეავსეთ ეს ტესტი, რათა შეამოწმოთ გაკვეთილის მასალის გაგება.'
        };
    }
}
