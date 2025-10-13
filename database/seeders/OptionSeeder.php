<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class OptionSeeder extends Seeder
{
    public function run()
    {
        // ОГРАНИЧЕНИЕ: только первые 200 вопросов для тестирования
        $questions = Question::whereIn('type', ['single', 'multiple'])
                            ->orderBy('id')
                            ->limit(200) // ← УМЕНЬШИЛИ ДО 200
                            ->get(['id', 'type']);

        $options = [];
        $processedQuestions = [];

        foreach ($questions as $question) {
            // Пропускаем вопросы, для которых уже созданы опции
            if (in_array($question->id, $processedQuestions)) {
                continue;
            }

            $optionsCount = $question->type === 'single' ? 4 : 6;
            $correctOptions = $question->type === 'single' ? 1 : rand(1, 2);

            $correctIndices = $this->getRandomIndices($optionsCount, $correctOptions);

            for ($i = 0; $i < $optionsCount; $i++) {
                $options[] = [
                    'question_id' => $question->id,
                    'key' => chr(97 + $i), // a, b, c, d, e, f
                    'is_correct' => in_array($i, $correctIndices) ? 1 : 0,
                    'order' => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $processedQuestions[] = $question->id;

            // Вставляем пачками по 100 записей (уменьшили размер пачки)
            if (count($options) >= 100) {
                try {
                    Option::insert($options);
                    $options = [];
                } catch (\Exception $e) {
                    // Если ошибка уникальности, пропускаем эту пачку
                    echo "Skipping batch due to unique constraint...\n";
                    $options = [];
                }
            }
        }

        // Вставляем оставшиеся записи
        if (!empty($options)) {
            try {
                Option::insert($options);
            } catch (\Exception $e) {
                echo "Skipping final batch due to unique constraint...\n";
            }
        }

        echo "Options created for " . count($processedQuestions) . " questions.\n";
        echo "Creating translations...\n";

        // Переводы создаем отдельно
        $this->createOptionTranslations();
    }

    private function getRandomIndices(int $total, int $count): array
    {
        $indices = range(0, $total - 1);
        shuffle($indices);
        return array_slice($indices, 0, $count);
    }

    private function createOptionTranslations(): void
    {
        // Берем только что созданные опции
        $options = Option::orderBy('id', 'desc')
                        ->limit(800) // 200 вопросов × 4 опции в среднем
                        ->get();

        $translations = [];

        foreach ($options as $option) {
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                $translations[] = [
                    'option_id' => $option->id,
                    'locale' => $locale,
                    'text' => $this->generateOptionText($locale, $option->order, $option->question_id, $option->is_correct),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($translations) >= 300) {
                    try {
                        DB::table('option_translations')->insert($translations);
                        $translations = [];
                    } catch (\Exception $e) {
                        echo "Skipping translation batch...\n";
                        $translations = [];
                    }
                }
            }
        }

        if (!empty($translations)) {
            try {
                DB::table('option_translations')->insert($translations);
            } catch (\Exception $e) {
                echo "Skipping final translation batch...\n";
            }
        }

        echo "Option translations created successfully.\n";
    }

    private function generateOptionText(string $locale, int $index, int $questionId, bool $isCorrect): string
    {
        $baseOptions = match($locale) {
            'en' => [
                'To track code changes and collaborate',
                'Object-oriented programming only',
                'Organizing code into classes and objects',
                'Better performance and scalability',
                'Using asynchronous functions',
                'Model-View-Controller pattern',
            ],
            'ru' => [
                'Отслеживать изменения кода и сотрудничать',
                'Только объектно-ориентированное программирование',
                'Организация кода в классы и объекты',
                'Лучшая производительность и масштабируемость',
                'Использование асинхронных функций',
                'Паттерн Модель-Представление-Контроллер',
            ],
            'ka' => [
                'კოდის ცვლილებების თვალყურის დევნება და თანამშრომლობა',
                'მხოლოდ ობიექტზე-ორიენტირებული პროგრამირება',
                'კოდის ორგანიზება კლასებად და ობიექტებად',
                'უკეთესი შესრულება და მასშტაბირება',
                'ასინქრონული ფუნქციების გამოყენება',
                'მოდელი-ხედი-კონტროლერის ნიმუში',
            ]
        };

        $optionIndex = ($questionId + $index) % count($baseOptions);
        $optionText = $baseOptions[$optionIndex];

        return $isCorrect ? $optionText . " ✓" : $optionText;
    }
}
