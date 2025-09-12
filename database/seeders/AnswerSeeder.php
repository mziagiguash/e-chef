<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Str;

class AnswerSeeder extends Seeder
{
    public function run()
    {
        $questions = Question::all();

        if ($questions->isEmpty()) {
            // Если вопросов нет, создаем демо вопросы
            $this->command->info('No questions found! Creating demo questions...');

            $demoQuestions = [
                'Что такое Laravel?',
                'Что такое MVC паттерн?',
                'Какие преимущества у PHP 8?',
                'Что такое миграции в Laravel?',
                'Как работает Eloquent ORM?'
            ];

            foreach ($demoQuestions as $questionText) {
                Question::create([
                    'text' => $questionText,
                    'type' => 'multiple_choice',
                    'points' => 5,
                    'quiz_id' => 1 // или null, если не требуется
                ]);
            }

            $questions = Question::all();
        }

        foreach ($questions as $question) {
            // Создаем 1 правильный ответ
            Answer::create([
                'question_id' => $question->id,
                'text' => 'Правильный ответ на: ' . $question->text,
                'is_correct' => true,
                'order' => 1,
                'explanation' => 'Это правильный вариант ответа'
            ]);

            // Создаем 3 неправильных ответа
            for ($i = 2; $i <= 4; $i++) {
                Answer::create([
                    'question_id' => $question->id,
                    'text' => 'Неправильный вариант ' . ($i-1) . ' для: ' . $question->text,
                    'is_correct' => false,
                    'order' => $i,
                    'explanation' => null
                ]);
            }
        }

        $this->command->info('Answers seeded successfully! Created: ' . Answer::count() . ' answers');
    }
}
