<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\LessonTranslation;
use App\Models\Course;
use App\Models\Quiz;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем существующие курсы
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->info('No courses found. Please seed courses first.');
            return;
        }

        // Получаем существующие квизы
        $quizzes = Quiz::all();
        $quizIds = $quizzes->pluck('id')->toArray();

        $totalLessons = 0;

        foreach ($courses as $course) {
            // Создаем 8-12 уроков для каждого курса
            $lessonCount = rand(8, 12);

            for ($i = 0; $i < $lessonCount; $i++) {
                $lessonNumber = $i + 1;

                // Каждый 3-4 урок получает квиз
                $quizId = ($lessonNumber % 3 === 0 || $lessonNumber % 4 === 0) && !empty($quizIds)
                    ? $quizIds[array_rand($quizIds)]
                    : null;

                $lesson = Lesson::create([
                    'title' => json_encode([
                        'en' => "Lesson {$lessonNumber}: " . $this->getLessonTitle('en', $i),
                        'ru' => "Урок {$lessonNumber}: " . $this->getLessonTitle('ru', $i),
                        'ka' => "გაკვეთილი {$lessonNumber}: " . $this->getLessonTitle('ka', $i)
                    ]),
                    'course_id' => $course->id,
                    'quiz_id' => $quizId,
                    'description' => json_encode([
                        'en' => "This is lesson {$lessonNumber} of the course. Learn important concepts and techniques.",
                        'ru' => "Это урок {$lessonNumber} курса. Изучите важные концепции и техники.",
                        'ka' => "ეს არის კურსის {$lessonNumber} გაკვეთილი. ისწავლეთ მნიშვნელოვანი კონცეფციები და ტექნიკა."
                    ]),
                    'notes' => json_encode([
                        'en' => "Key points for lesson {$lessonNumber}",
                        'ru' => "Ключевые моменты урока {$lessonNumber}",
                        'ka' => "გაკვეთილის {$lessonNumber} ძირითადი მომენტები"
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalLessons++;
            }

            $this->command->info("Created {$lessonCount} lessons for course ID: {$course->id}");
        }

        $this->command->info("✅ Total {$totalLessons} lessons seeded successfully!");
        $this->command->info("📊 For {$courses->count()} courses");
    }

    private function getLessonTitle(string $locale, int $index): string
    {
        $titles = [
            'en' => [
                'Introduction to the Course',
                'Setting Up Development Environment',
                'Basic Concepts and Fundamentals',
                'Advanced Techniques',
                'Practical Examples',
                'Project Setup',
                'Debugging and Troubleshooting',
                'Best Practices',
                'Performance Optimization',
                'Security Considerations',
                'Deployment Strategies',
                'Testing Methodology'
            ],
            'ru' => [
                'Введение в курс',
                'Настройка среды разработки',
                'Основные концепции и основы',
                'Продвинутые техники',
                'Практические примеры',
                'Настройка проекта',
                'Отладка и решение проблем',
                'Лучшие практики',
                'Оптимизация производительности',
                'Вопросы безопасности',
                'Стратегии развертывания',
                'Методология тестирования'
            ],
            'ka' => [
                'კურსში გაცნობა',
                'განვითარების გარემოს დაყენება',
                'ძირითადი კონცეფციები და საფუძვლები',
                'მოწინავე ტექნიკა',
                'პრაქტიკული მაგალითები',
                'პროექტის დაყენება',
                'დებაგინგი და პრობლემების გადაჭრა',
                'საუკეთესო პრაქტიკები',
                'შესრულების ოპტიმიზაცია',
                'უსაფრთხოების მოსაზრებები',
                'დეპლოიმენტის სტრატეგიები',
                'ტესტირების მეთოდოლოგია'
            ]
        ];

        return $titles[$locale][$index % count($titles[$locale])];
    }
}
