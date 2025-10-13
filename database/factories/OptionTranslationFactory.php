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
            'text' => $this->generateOptionText($locale),
        ];
    }

    private function generateOptionText(string $locale): string
    {
        $options = match($locale) {
            'en' => [
                'To track changes in code',
                'Both object-oriented and functional',
                'Organizing code around objects',
                'Data integrity and relationships',
                'Using callbacks and promises',
                'Separates concerns into Model, View, Controller',
                'HTTPS is secure with encryption',
                'Adapts to different screen sizes',
                'Readability and maintainability',
                'Reduces database queries'
            ],
            'ru' => [
                'Отслеживать изменения в коде',
                'И объектно-ориентированное, и функциональное',
                'Организация кода вокруг объектов',
                'Целостность данных и отношения',
                'Использование колбэков и промисов',
                'Разделяет на Модель, Представление, Контроллер',
                'HTTPS безопасен с шифрованием',
                'Адаптируется к разным размерам экрана',
                'Читаемость и поддерживаемость',
                'Уменьшает запросы к базе данных'
            ],
            'ka' => [
                'კოდში ცვლილებების თვალყურის დევნება',
                'ორივე ობიექტზე-ორიენტირებული და ფუნქციური',
                'კოდის ორგანიზება ობიექტების გარშემო',
                'მონაცემთა მთლიანობა და ურთიერთობები',
                'კოლბექების და პრომისების გამოყენება',
                'ჰყოფს მოდელს, ხედს და კონტროლერს',
                'HTTPS არის უსაფრთხო დაშიფვრით',
                'ეგუოდება სხვადასხვა ეკრანის ზომას',
                'წაკითხვადობა და მოვლა',
                'ამცირებს მონაცემთა ბაზის მოთხოვნებს'
            ]
        };

        return $options[array_rand($options)];
    }

    public function english(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'en',
                'text' => $this->generateOptionText('en'),
            ];
        });
    }

    public function russian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ru',
                'text' => $this->generateOptionText('ru'),
            ];
        });
    }

    public function georgian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ka',
                'text' => $this->generateOptionText('ka'),
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
