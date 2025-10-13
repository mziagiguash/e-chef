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
        $locale = fake()->randomElement(['en', 'ru', 'ka']);

        return [
            'lesson_id' => Lesson::factory(),
            'locale' => $locale,
            'title' => $this->generateTitle($locale),
            'description' => $this->generateDescription($locale),
            'notes' => $this->generateNotes($locale),
        ];
    }

    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => [
                'Introduction to Programming', 'Variables and Data Types', 'Control Structures',
                'Functions and Methods', 'Object-Oriented Programming', 'Error Handling',
                'File Operations', 'Database Connectivity', 'Web Development Basics',
                'API Development', 'Testing Strategies', 'Deployment Techniques'
            ],
            'ru' => [
                'Введение в программирование', 'Переменные и типы данных', 'Управляющие структуры',
                'Функции и методы', 'Объектно-ориентированное программирование', 'Обработка ошибок',
                'Работа с файлами', 'Подключение к базам данных', 'Основы веб-разработки',
                'Разработка API', 'Стратегии тестирования', 'Техники развертывания'
            ],
            'ka' => [
                'პროგრამირების შესავალი', 'ცვლადები და მონაცემთა ტიპები', 'კონტროლის სტრუქტურები',
                'ფუნქციები და მეთოდები', 'ობიექტზე-ორიენტირებული პროგრამირება', 'შეცდომების დამუშავება',
                'ფაილური ოპერაციები', 'მონაცემთა ბაზების დაკავშირება', 'ვებ-განვითარების საფუძვლები',
                'API-ის განვითარება', 'ტესტირების სტრატეგიები', 'დეპლოიმენტის ტექნიკა'
            ]
        };

        $lessonNumber = fake()->numberBetween(1, 20);
        return $titles[array_rand($titles)] . " - Part {$lessonNumber}";
    }

    private function generateDescription(string $locale): string
    {
        return match($locale) {
            'en' => fake()->paragraphs(3, true) . ' Learn practical skills and apply them in real projects.',
            'ru' => fake()->paragraphs(3, true) . ' Изучайте практические навыки и применяйте их в реальных проектах.',
            'ka' => fake()->paragraphs(3, true) . ' ისწავლეთ პრაქტიკული უნარები და გამოიყენეთ ისინი რეალურ პროექტებში.'
        };
    }

    private function generateNotes(string $locale): string
    {
        $notes = match($locale) {
            'en' => [
                "Key points to remember:\n• Practice regularly\n• Review previous lessons\n• Don't hesitate to ask questions",
                "Important notes:\n• Take your time with exercises\n• Experiment with code\n• Join community discussions"
            ],
            'ru' => [
                "Ключевые моменты:\n• Регулярно практикуйтесь\n• Повторяйте предыдущие уроки\n• Не стесняйтесь задавать вопросы",
                "Важные заметки:\n• Не торопитесь с упражнениями\n• Экспериментируйте с кодом\n• Присоединяйтесь к обсуждениям в сообществе"
            ],
            'ka' => [
                "საკვანძო პუნქტები:\n• რეგულარული პრაქტიკა\n• წინა გაკვეთილების მიმოხილვა\n• შეკითხვების დასმაში ნუ დაგეგდებთ",
                "მნიშვნელოვანი შენიშვნები:\n• დრო დაუთმეთ სავარჯიშოებს\n• ექსპერიმენტები განახორციელეთ კოდთან\n• შეუერთდით საზოგადოების განხილვებს"
            ]
        };

        return $notes[array_rand($notes)] . "\n\nAdditional resources: " . fake()->url();
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
