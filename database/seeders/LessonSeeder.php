<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use Illuminate\Support\Str;

class LessonSeeder extends Seeder
{
    public function run()
    {
        $courses = \App\Models\Course::all();

        foreach ($courses as $course) {
            $lessonsCount = $course->lesson; // Используем количество уроков из курса

            for ($i = 1; $i <= $lessonsCount; $i++) {
                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'quiz_id' => null, // Будет установлено в QuizSeeder
                    'video' => 'https://example.com/videos/lesson-' . $i . '.mp4',
                    'materials' => $this->generateMaterials(),
                    'order' => $i,
                    'is_active' => true,
                ]);

                // Создаем переводы для урока
                $locales = ['en', 'ru', 'ka'];

                foreach ($locales as $locale) {
                    $lesson->translations()->create([
                        'locale' => $locale,
                        'title' => $this->generateLessonTitle($locale, $i),
                        'description' => $this->generateLessonDescription($locale),
                        'notes' => $this->generateLessonNotes($locale),
                    ]);
                }
            }
        }
    }

    private function generateLessonTitle(string $locale, int $order): string
    {
        $titles = match($locale) {
            'en' => [
                "Introduction to Course Concepts",
                "Advanced Techniques and Methods",
                "Practical Implementation Guide",
                "Best Practices and Patterns",
                "Real-world Applications",
                "Troubleshooting and Debugging",
                "Performance Optimization",
                "Project Setup and Configuration",
                "Testing Strategies",
                "Deployment and Maintenance"
            ],
            'ru' => [
                "Введение в концепции курса",
                "Продвинутые техники и методы",
                "Руководство по практической реализации",
                "Лучшие практики и паттерны",
                "Применение в реальных проектах",
                "Поиск и устранение неисправностей",
                "Оптимизация производительности",
                "Настройка и конфигурация проекта",
                "Стратегии тестирования",
                "Развертывание и поддержка"
            ],
            'ka' => [
                "კურსის კონცეფციების შესავალი",
                "მოწინავე ტექნიკა და მეთოდები",
                "პრაქტიკული განხორციელების სახელმძღვანელო",
                "საუკეთესო პრაქტიკები და ნიმუშები",
                "რეალური პროექტების გამოყენება",
                "ხარვეზების აღმოფხვრა და დებაგინგი",
                "წარმადობის ოპტიმიზაცია",
                "პროექტის დაყენება და კონფიგურაცია",
                "ტესტირების სტრატეგიები",
                "განლაგება და მხარდაჭერა"
            ]
        };

        return "Lesson {$order}: " . $titles[array_rand($titles)];
    }

    private function generateLessonDescription(string $locale): string
    {
        $descriptions = match($locale) {
            'en' => [
                "In this lesson, you will learn the fundamental concepts that form the basis of the entire course. We'll cover essential terminology and basic principles.",
                "This lesson focuses on practical implementation of the concepts discussed. You'll work through real-world examples and build your skills step by step.",
                "Advanced techniques and optimization strategies are the main focus here. Learn how to improve performance and write more efficient code.",
                "Best practices and industry standards are covered in depth. Understand how professionals approach common challenges and solutions."
            ],
            'ru' => [
                "В этом уроке вы изучите фундаментальные концепции, которые составляют основу всего курса. Мы рассмотрим основную терминологию и базовые принципы.",
                "Этот урок посвящен практической реализации обсуждаемых концепций. Вы будете работать с реальными примерами и постепенно развивать свои навыки.",
                "Основное внимание здесь уделяется продвинутым техникам и стратегиям оптимизации. Узнайте, как улучшить производительность и писать более эффективный код.",
                "Лучшие практики и отраслевые стандарты рассматриваются подробно. Поймите, как профессионалы подходят к общим проблемам и решениям."
            ],
            'ka' => [
                "ამ გაკვეთილში თქვენ შეისწავლით ფუნდამენტურ კონცეფციებს, რომლებიც მთელი კურსის საფუძველს წარმოადგენს. ჩვენ განვიხილავთ ძირითად ტერმინოლოგიას და ძირითად პრინციპებს.",
                "ეს გაკვეთილი ორიენტირებულია განხილული კონცეფციების პრაქტიკულ განხორციელებაზე. თქვენ იმუშავებთ რეალურ მაგალითებზე და ეტაპობრივად განავითარებთ თქვენს უნარებს.",
                "აქ ძირითადი ყურადღება ექცევა მოწინავე ტექნიკას და ოპტიმიზაციის სტრატეგიებს. გაიგეთ, როგორ გააუმჯობესოთ შესრულება და დაწეროთ უფრო ეფექტური კოდი.",
                "საუკეთესო პრაქტიკები და ინდუსტრიული სტანდარტები დეტალურად არის განხილული. გაიგეთ, როგორ უახლოვდებიან პროფესიონალები საერთო გამოწვევებს და გადაწყვეტილებებს."
            ]
        };

        return $descriptions[array_rand($descriptions)];
    }

    private function generateLessonNotes(string $locale): string
    {
        $notes = match($locale) {
            'en' => [
                "Key points to remember:\n• Practice regularly\n• Review previous lessons\n• Don't hesitate to ask questions",
                "Important notes:\n• Take your time with exercises\n• Experiment with code\n• Join community discussions",
                "Study tips:\n• Create cheat sheets\n• Work on mini-projects\n• Teach others what you learn"
            ],
            'ru' => [
                "Ключевые моменты:\n• Регулярно практикуйтесь\n• Повторяйте предыдущие уроки\n• Не стесняйтесь задавать вопросы",
                "Важные заметки:\n• Не торопитесь с упражнениями\n• Экспериментируйте с кодом\n• Присоединяйтесь к обсуждениям в сообществе",
                "Советы по изучению:\n• Создавайте шпаргалки\n• Работайте над мини-проектами\n• Учите других тому, что узнали"
            ],
            'ka' => [
                "საკვანძო პუნქტები:\n• რეგულარული პრაქტიკა\n• წინა გაკვეთილების მიმოხილვა\n• შეკითხვების დასმაში ნუ დაგეგდებთ",
                "მნიშვნელოვანი შენიშვნები:\n• დრო დაუთმეთ სავარჯიშოებს\n• ექსპერიმენტები განახორციელეთ კოდთან\n• შეუერთდით საზოგადოების განხილვებს",
                "სწავლის რჩევები:\n• შექმენით მოხსენებები\n• იმუშავეთ მინი-პროექტებზე\n• სხვებს ასწავლეთ ის, რაც ისწავლეთ"
            ]
        };

        $urls = [
            'https://example.com/resources/lesson-notes.pdf',
            'https://example.com/docs/additional-reading',
            'https://example.com/videos/supplementary-materials'
        ];

        return $notes[array_rand($notes)] . "\n\nAdditional resources: " . $urls[array_rand($urls)];
    }

    private function generateMaterials(): string
    {
        $materials = [
            "Download the exercise files from the resources section. Complete all practice exercises before moving to the next lesson.",
            "Reference materials include code samples, documentation links, and recommended reading lists.",
            "Practice projects and real-world examples are provided to help reinforce the concepts learned in this lesson.",
            "Additional resources include video tutorials, cheat sheets, and community forum links for further learning."
        ];

        return $materials[array_rand($materials)];
    }
}
