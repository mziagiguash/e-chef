<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;
use App\Models\OptionTranslation;
use App\Models\Question;

class OptionSeeder extends Seeder
{
    public function run()
    {
        // Получаем вопросы, которые требуют опций (multiple choice)
        $questions = Question::whereIn('type', ['single', 'multiple'])
                            ->whereDoesntHave('options')
                            ->get();

        if ($questions->isEmpty()) {
            $this->command->info('No questions found that need options. Please run QuestionSeeder first.');
            return;
        }

        $createdOptions = 0;
        $createdTranslations = 0;

        foreach ($questions as $question) {
            $optionCount = rand(3, 5);
            $correctCount = $question->type === 'single' ? 1 : rand(1, 2);

            for ($i = 1; $i <= $optionCount; $i++) {
                $isCorrect = $i <= $correctCount;

                // Создаем опцию
                $option = Option::create([
                    'question_id' => $question->id,
                    'is_correct' => $isCorrect,
                    'order' => $i
                ]);

                $createdOptions++;

                // Создаем переводы для всех языков
                foreach (['en', 'ru', 'ka'] as $locale) {
                    OptionTranslation::create([
                        'option_id' => $option->id,
                        'locale' => $locale,
                        'option_text' => $this->generateOptionText($locale, $i, $isCorrect)
                    ]);

                    $createdTranslations++;
                }
            }
        }

        $this->command->info("Options seeded successfully.");
        $this->command->info("Created: {$createdOptions} options");
        $this->command->info("Created: {$createdTranslations} translations");
    }

    private function generateOptionText(string $locale, int $order, bool $isCorrect): string
    {
        $prefix = $isCorrect ? '[CORRECT] ' : '';

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

        return $prefix . $options[array_rand($options)] . " (Option #{$order})";
    }
}
