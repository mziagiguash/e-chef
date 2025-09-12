<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\Quiz;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем существующие квизы
        $quizzes = Quiz::all();

        if ($quizzes->isEmpty()) {
            $this->command->info('No quizzes found. Please seed quizzes first.');
            return;
        }

        $totalQuestions = 0;

        foreach ($quizzes as $quiz) {
            // Создаем 5-10 вопросов для каждого квиза
            $questionCount = rand(5, 10);

            for ($i = 1; $i <= $questionCount; $i++) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'content' => "Question {$i}: What is the main concept discussed in this section?",
                    'type' => 'single',
                    'points' => 1,
                    'order' => $i,
                    'is_required' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Создаем переводы для вопроса
                $this->createQuestionTranslations($question, $i);

                $totalQuestions++;
            }

            $this->command->info("Created {$questionCount} questions for quiz ID: {$quiz->id}");
        }

        $this->command->info("✅ Total {$totalQuestions} questions with translations seeded successfully!");
        $this->command->info("📊 For {$quizzes->count()} quizzes");
    }

    private function createQuestionTranslations($question, int $index): void
    {
        $translations = [
            [
                'locale' => 'en',
                'content' => "Question {$index}: What is the main concept discussed in this section?"
            ],
            [
                'locale' => 'ru',
                'content' => "Вопрос {$index}: Какая основная концепция обсуждается в этом разделе?"
            ],
            [
                'locale' => 'ka',
                'content' => "კითხვა {$index}: რა არის მთავარი კონცეფცია, რომელიც განიხილება ამ განყოფილებაში?"
            ]
        ];

        foreach ($translations as $translation) {
            QuestionTranslation::create([
                'question_id' => $question->id,
                'locale' => $translation['locale'],
                'content' => $translation['content'], // Соответствует полю в миграции
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
