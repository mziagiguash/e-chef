<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OptionTranslation;
use App\Models\Option;

class OptionTranslationFactory extends Factory
{
    protected $model = OptionTranslation::class;

    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'ru', 'ka']);

        return [
            'option_id' => Option::factory(),
            'locale' => $locale,
            'option_text' => $this->generateOptionText($locale),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Генерирует текст опции в зависимости от языка
     */
    private function generateOptionText(string $locale): string
    {
        $options = match($locale) {
            'en' => [
                'To track changes in code',
                'Yes, it supports multiple paradigms',
                'Classes and objects',
                'Data integrity and relationships',
                'Using callbacks and promises',
                'Model-View-Controller separation',
                'HTTPS provides encryption',
                'Adapts to different screen sizes',
                'Readability and maintainability',
                'Reduces database queries'
            ],
            'ru' => [
                'Для отслеживания изменений в коде',
                'Да, поддерживает несколько парадигм',
                'Классы и объекты',
                'Целостность данных и отношения',
                'С использованием колбэков и промисов',
                'Разделение Model-View-Controller',
                'HTTPS обеспечивает шифрование',
                'Адаптируется к разным размерам экранов',
                'Читаемость и поддерживаемость',
                'Уменьшает количество запросов к базе данных'
            ],
            'ka' => [
                'კოდში ცვლილებების თვალყურის დევნებისთვის',
                'დიახ, მხარს უჭერს მრავალ პარადიგმას',
                'კლასები და ობიექტები',
                'მონაცემთა მთლიანობა და ურთიერთობები',
                'კოლბეკების და პრომისების გამოყენებით',
                'Model-View-Controller გაყოფა',
                'HTTPS უზრუნველყოფს დაშიფვრას',
                'ეგუოდება სხვადასხვა ეკრანის ზომებს',
                'წაკითხვადობა და მოვლა',
                'ამცირებს მონაცემთა ბაზის მოთხოვნებს'
            ]
        };

        return $options[array_rand($options)];
    }

    // Состояния для конкретных локалей
    public function english(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'en',
                'option_text' => $this->generateOptionText('en'),
            ];
        });
    }

    public function russian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ru',
                'option_text' => $this->generateOptionText('ru'),
            ];
        });
    }

    public function georgian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ka',
                'option_text' => $this->generateOptionText('ka'),
            ];
        });
    }

    public function forOption(Option $option): static
    {
        return $this->state(function (array $attributes) use ($option) {
            return [
                'option_id' => $option->id,
            ];
        });
    }
}
