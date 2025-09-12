<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LessonTranslation;
use App\Models\Lesson;

class LessonTranslationFactory extends Factory
{
    protected $model = LessonTranslation::class;

    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'ru', 'ka']);

        return [
            'lesson_id' => Lesson::factory(),
            'locale' => $locale,
            'title' => $this->generateTitle($locale),
            'description' => $this->generateDescription($locale),
            'notes' => $this->generateNotes($locale),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => [
                'Introduction to Programming', 'Variables and Data Types', 'Control Structures',
                'Functions and Methods', 'Object-Oriented Programming', 'Error Handling',
                'File Operations', 'Database Connectivity', 'Web Development Basics',
                'API Development', 'Testing Strategies', 'Deployment Techniques',
                'Performance Optimization', 'Security Best Practices', 'Debugging Methods',
                'Version Control', 'Code Review', 'Design Patterns', 'Algorithms', 'Data Structures'
            ],
            'ru' => [
                'Введение в программирование', 'Переменные и типы данных', 'Управляющие структуры',
                'Функции и методы', 'Объектно-ориентированное программирование', 'Обработка ошибок',
                'Работа с файлами', 'Подключение к базам данных', 'Основы веб-разработки',
                'Разработка API', 'Стратегии тестирования', 'Техники развертывания',
                'Оптимизация производительности', 'Лучшие практики безопасности', 'Методы отладки',
                'Система контроля версий', 'Ревью кода', 'Шаблоны проектирования', 'Алгоритмы', 'Структуры данных'
            ],
            'ka' => [
                'პროგრამირების შესავალი', 'ცვლადები და მონაცემთა ტიპები', 'კონტროლის სტრუქტურები',
                'ფუნქციები და მეთოდები', 'ობიექტზე-ორიენტირებული პროგრამირება', 'შეცდომების დამუშავება',
                'ფაილური ოპერაციები', 'მონაცემთა ბაზების დაკავშირება', 'ვებ-განვითარების საფუძვლები',
                'API-ის განვითარება', 'ტესტირების სტრატეგიები', 'დეპლოიმენტის ტექნიკა',
                'შესრულების ოპტიმიზაცია', 'უსაფრთხოების საუკეთესო პრაქტიკები', 'დებაგინგის მეთოდები',
                'ვერსიების კონტროლი', 'კოდის რევიუ', 'დიზაინის პატერნები', 'ალგორითმები', 'მონაცემთა სტრუქტურები'
            ]
        };

        $lessonNumber = $this->faker->numberBetween(1, 20);
        return "{$titles[array_rand($titles)]} - Part {$lessonNumber}";
    }

    private function generateDescription(string $locale): string
    {
        return match($locale) {
            'en' => $this->faker->paragraphs(3, true) . ' Learn practical skills and apply them in real projects. This lesson covers essential concepts that every developer should know.',
            'ru' => $this->faker->paragraphs(3, true) . ' Изучайте практические навыки и применяйте их в реальных проектах. Этот урок охватывает основные концепции, которые должен знать каждый разработчик.',
            'ka' => $this->faker->paragraphs(3, true) . ' ისწავლეთ პრაქტიკული უნარები და გამოიყენეთ ისინი რეალურ პროექტებში. ეს გაკვეთილი მოიცავს ძირითად კონცეფციებს, რომელიც ყველა დეველოპერმა უნდა იცოდეს.'
        };
    }

    private function generateNotes(string $locale): string
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

        return $this->faker->randomElement($notes) . "\n\nAdditional resources: " . $this->faker->url();
    }

    public function english(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'en',
                'title' => $this->generateTitle('en'),
            ];
        });
    }

    public function russian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ru',
                'title' => $this->generateTitle('ru'),
            ];
        });
    }

    public function georgian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ka',
                'title' => $this->generateTitle('ka'),
            ];
        });
    }

    public function forLesson(Lesson $lesson): static
    {
        return $this->state(function (array $attributes) use ($lesson) {
            return [
                'lesson_id' => $lesson->id,
            ];
        });
    }
}
