<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;
use App\Models\OptionTranslation;
use App\Models\Question;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем существующие вопросы
        $questions = Question::all();

        if ($questions->isEmpty()) {
            $this->command->info('No questions found. Please seed questions first.');
            return;
        }

        $totalOptions = 0;

        foreach ($questions as $question) {
            // Создаем 4 опции для каждого вопроса
            $correctIndex = rand(0, 3); // Случайный индекс для правильного ответа

            for ($i = 0; $i < 4; $i++) {
                $isCorrect = ($i === $correctIndex);

                // Создаем опцию (без текста, только метаданные)
                $option = Option::create([
                    'question_id' => $question->id,
                    'is_correct' => $isCorrect,
                    'order' => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Создаем переводы для опции
                $this->createOptionTranslations($option, $i, $isCorrect);

                $totalOptions++;
            }

            $this->command->info("Created 4 options for question ID: {$question->id}");
        }

        $this->command->info("✅ Total {$totalOptions} options with translations seeded successfully!");
        $this->command->info("📊 For {$questions->count()} questions");
    }

    private function createOptionTranslations($option, int $index, bool $isCorrect): void
    {
        $locales = ['en', 'ru', 'ka'];

        foreach ($locales as $locale) {
            OptionTranslation::create([
                'option_id' => $option->id,
                'locale' => $locale,
                'option_text' => $this->generateOptionText($locale, $index, $isCorrect),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateOptionText(string $locale, int $index, bool $isCorrect): string
    {
        $options = [
            'en' => [
                'Correct answer for this question',
                'Incorrect option A',
                'Incorrect option B',
                'Incorrect option C'
            ],
            'ru' => [
                'Правильный ответ на этот вопрос',
                'Неправильный вариант A',
                'Неправильный вариант B',
                'Неправильный вариант C'
            ],
            'ka' => [
                'სწორი პასუხი ამ კითხვაზე',
                'არასწორი ვარიანტი A',
                'არასწორი ვარიანტი B',
                'არასწორი ვარიანტი C'
            ]
        ];

        return $options[$locale][$index];
    }
}
