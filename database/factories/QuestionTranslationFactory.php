<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\QuestionTranslation;
use App\Models\Question;

class QuestionTranslationFactory extends Factory
{
    protected $model = QuestionTranslation::class;

    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'ru', 'ka']);

        return [
            'question_id' => Question::factory(),
            'locale' => $locale,
            'content' => $this->generateContent($locale),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Генерирует содержание вопроса в зависимости от языка
     */
    private function generateContent(string $locale): string
    {
        $questions = match($locale) {
            'en' => [
                'What is the main purpose of version control systems?',
                'Which programming paradigm does Python support?',
                'Explain the concept of object-oriented programming.',
                'What are the advantages of using a relational database?',
                'How does JavaScript handle asynchronous operations?',
                'Describe the MVC architectural pattern.',
                'What is the difference between HTTP and HTTPS?',
                'Explain the concept of responsive web design.',
                'What are the principles of clean code?',
                'How does caching improve application performance?'
            ],
            'ru' => [
                'Какова основная цель систем контроля версий?',
                'Какую парадигму программирования поддерживает Python?',
                'Объясните концепцию объектно-ориентированного программирования.',
                'Каковы преимущества использования реляционной базы данных?',
                'Как JavaScript обрабатывает асинхронные операции?',
                'Опишите архитектурный паттерн MVC.',
                'В чем разница между HTTP и HTTPS?',
                'Объясните концепцию адаптивного веб-дизайна.',
                'Каковы принципы чистого кода?',
                'Как кэширование улучшает производительность приложения?'
            ],
            'ka' => [
                'რა არის ვერსიების კონტროლის სისტემების მთავარი მიზანი?',
                'რომელ პროგრამირების პარადიგმას უჭერს მხარს Python?',
                'ახსენით ობიექტზე-ორიენტირებული პროგრამირების კონცეფცია.',
                'რა უპირატესობები აქვს რელაციური მონაცემთა ბაზის გამოყენებას?',
                'როგორ ამუშავებს JavaScript ასინქრონულ ოპერაციებს?',
                'აღწერეთ MVC არქიტექტურული ნიმუში.',
                'რა განსხვავებაა HTTP-სა და HTTPS-ს შორის?',
                'ახსენით რესპონსიული ვებ-დიზაინის კონცეფცია.',
                'რა არის სუფთა კოდის პრინციპები?',
                'როგორ უმჯობესებს კეშირება აპლიკაციის მუშაობას?'
            ]
        };

        return $questions[array_rand($questions)];
    }

    // Состояния для конкретных локалей
    public function english(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'en',
                'content' => $this->generateContent('en'),
            ];
        });
    }

    public function russian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ru',
                'content' => $this->generateContent('ru'),
            ];
        });
    }

    public function georgian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ka',
                'content' => $this->generateContent('ka'),
            ];
        });
    }

    public function forQuestion(Question $question): static
    {
        return $this->state(function (array $attributes) use ($question) {
            return [
                'question_id' => $question->id,
            ];
        });
    }
}
