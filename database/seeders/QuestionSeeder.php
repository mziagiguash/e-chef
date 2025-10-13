<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Quiz;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $quizzes = Quiz::all();

        foreach ($quizzes as $quiz) {
            $questionsCount = $quiz->questions_count;

            for ($i = 1; $i <= $questionsCount; $i++) {
                $type = $this->getQuestionType($i);

                // Устанавливаем значения для ВСЕХ полей (они NOT NULL)
                $minRating = 1; // значение по умолчанию для всех типов
                $maxRating = 5; // значение по умолчанию для всех типов
                $maxChoices = 1; // значение по умолчанию

                if ($type === 'multiple') {
                    $maxChoices = rand(2, 3);
                } elseif ($type === 'rating') {
                    // Для rating вопросов оставляем 1-5
                    $minRating = 1;
                    $maxRating = 5;
                } else {
                    // Для single, text вопросов устанавливаем минимальные значения
                    $minRating = 1;
                    $maxRating = 1;
                }

                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'type' => $type,
                    'order' => $i,
                    'points' => $this->getQuestionPoints($type),
                    'is_required' => true,
                    'max_choices' => $maxChoices,
                    'min_rating' => $minRating, // НЕ МОЖЕТ БЫТЬ NULL
                    'max_rating' => $maxRating, // НЕ МОЖЕТ БЫТЬ NULL
                ]);

                // Создаем переводы вопроса
                $this->createQuestionTranslations($question, $i);
            }
        }
    }

    private function getQuestionType(int $order): string
    {
        // Чередуем типы вопросов
        $types = ['single', 'multiple', 'text', 'rating'];
        return $types[($order - 1) % count($types)];
    }

    private function getQuestionPoints(string $type): int
    {
        return match($type) {
            'single' => 1,
            'multiple' => 2,
            'text' => 3,
            'rating' => 1,
            default => 1
        };
    }

    private function createQuestionTranslations(Question $question, int $order): void
    {
        $locales = ['en', 'ru', 'ka'];

        foreach ($locales as $locale) {
            $question->translations()->create([
                'locale' => $locale,
                'content' => $this->generateQuestionContent($locale, $order, $question->type),
                'explanation' => $this->generateExplanation($locale),
            ]);
        }
    }

    // ... остальные методы без изменений
    private function generateQuestionContent(string $locale, int $order, string $type): string
    {
        $baseQuestions = match($locale) {
            'en' => [
                "What is the main purpose of {$this->getTopic($order)}?",
                "Which statement best describes {$this->getTopic($order)}?",
                "Explain the concept of {$this->getTopic($order)}.",
                "How would you rate your understanding of {$this->getTopic($order)}?",
                "Select all that apply to {$this->getTopic($order)}:",
            ],
            'ru' => [
                "Какова основная цель {$this->getTopic($order, 'ru')}?",
                "Какое утверждение лучше всего описывает {$this->getTopic($order, 'ru')}?",
                "Объясните концепцию {$this->getTopic($order, 'ru')}.",
                "Как вы оцениваете свое понимание {$this->getTopic($order, 'ru')}?",
                "Выберите все, что относится к {$this->getTopic($order, 'ru')}:",
            ],
            'ka' => [
                "რა არის {$this->getTopic($order, 'ka')} მთავარი მიზანი?",
                "რომელი განცხადება საუკეთესოდ აღწერს {$this->getTopic($order, 'ka')}?",
                "ახსენით {$this->getTopic($order, 'ka')} კონცეფცია.",
                "როგორ აფასებთ თქვენს გაგებას {$this->getTopic($order, 'ka')}?",
                "აირჩიეთ ყველა, რაც ეხება {$this->getTopic($order, 'ka')}:",
            ]
        };

        $question = $baseQuestions[array_rand($baseQuestions)];

        return match($type) {
            'single' => $question . " (Choose one correct answer)",
            'multiple' => $question . " (Select all correct answers)",
            'text' => $question . " (Write your answer)",
            'rating' => $question . " (Rate from 1 to 5)",
            default => $question
        };
    }

    private function getTopic(int $order, string $locale = 'en'): string
    {
        $topics = match($locale) {
            'en' => ['version control', 'object-oriented programming', 'database design', 'web development', 'API design'],
            'ru' => ['систем контроля версий', 'объектно-ориентированного программирования', 'проектирования баз данных', 'веб-разработки', 'проектирования API'],
            'ka' => ['ვერსიების კონტროლის', 'ობიექტზე-ორიენტირებული პროგრამირების', 'მონაცემთა ბაზების დიზაინის', 'ვებ-განვითარების', 'API-ის დიზაინის']
        };

        return $topics[($order - 1) % count($topics)];
    }

    private function generateExplanation(string $locale): string
    {
        return match($locale) {
            'en' => 'This question tests your understanding of key concepts.',
            'ru' => 'Этот вопрос проверяет ваше понимание ключевых концепций.',
            'ka' => 'ეს კითხვი ამოწმებს თქვენს გაგებას ძირითადი კონცეფციების.'
        };
    }
}
