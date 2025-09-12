<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем существующие курсы
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->info('No courses found. Please seed courses first.');
            return;
        }

        $quizCount = 0;

        // Создаем квизы для каждого курса (по 2-4 квиза на курс)
        foreach ($courses as $course) {
            $quizzesPerCourse = rand(2, 4);

            for ($i = 1; $i <= $quizzesPerCourse; $i++) {
                Quiz::create([
                    'title' => "Quiz {$i} - " . $this->getCourseTitle($course, 'en'),
                    'description' => "Test your knowledge of course concepts with this quiz.",
                    'time_limit' => rand(15, 45), // минут
                    'is_active' => true,
                    'passing_score' => rand(60, 80),
                    'max_attempts' => rand(1, 3), // исправлено: integer вместо boolean
                    'order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $quizCount++;
            }
        }

        $this->command->info("✅ Created {$quizCount} quizzes successfully!");
        $this->command->info("📊 For {$courses->count()} courses");
    }

    private function getCourseTitle($course, $locale): string
    {
        // Попробуем получить заголовок курса на нужном языке
        try {
            if (method_exists($course, 'getTranslation')) {
                return $course->getTranslation('title', $locale);
            }

            // Если метод getTranslation не существует, попробуем декодировать JSON
            $titleData = json_decode($course->title, true);
            return $titleData[$locale] ?? $titleData['en'] ?? 'Unknown Course';

        } catch (\Exception $e) {
            return 'Course ' . $course->id;
        }
    }
}
