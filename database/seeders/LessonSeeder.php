<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\LessonTranslation;
use App\Models\Course;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->info('No courses found. Please seed courses first.');
            return;
        }

        $totalLessons = 0;

        foreach ($courses as $course) {
            $lessonCount = rand(8, 12);

            for ($i = 0; $i < $lessonCount; $i++) {
                $lessonNumber = $i + 1;

                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'quiz_id' => null,
                    'order' => $lessonNumber, // теперь поле order существует
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Создаем переводы для урока
                $this->createLessonTranslations($lesson, $lessonNumber);
                $totalLessons++;
            }

            $this->command->info("Created {$lessonCount} lessons for course ID: {$course->id}");
        }

        $this->command->info("✅ Total {$totalLessons} lessons seeded successfully!");
        $this->command->info("📊 For {$courses->count()} courses");
    }

    private function createLessonTranslations(Lesson $lesson, int $lessonNumber): void
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

        $descriptions = [
            'en' => "This is lesson {$lessonNumber} of the course. Learn important concepts and techniques.",
            'ru' => "Это урок {$lessonNumber} курса. Изучите важные концепции и техники.",
            'ka' => "ეს არის კურსის {$lessonNumber} გაკვეთილი. ისწავლეთ მნიშვნელოვანი კონცეფციები და ტექნიკა."
        ];

        $notes = [
            'en' => "Key points for lesson {$lessonNumber}",
            'ru' => "Ключевые моменты урока {$lessonNumber}",
            'ka' => "გაკვეთილის {$lessonNumber} ძირითადი მომენტები"
        ];

        foreach (['en', 'ru', 'ka'] as $locale) {
            $titleIndex = ($lessonNumber - 1) % count($titles[$locale]);

            LessonTranslation::create([
                'lesson_id' => $lesson->id,
                'locale' => $locale,
                'title' => "Lesson {$lessonNumber}: " . $titles[$locale][$titleIndex],
                'description' => $descriptions[$locale],
                'notes' => $notes[$locale],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
