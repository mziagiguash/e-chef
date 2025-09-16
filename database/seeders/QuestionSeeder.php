<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Quiz;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Получаем все квизы
        $quizzes = Quiz::all();

        if ($quizzes->isEmpty()) {
            $this->command->info('No quizzes found. Please run QuizSeeder first.');
            return;
        }

        // Заменить:
$questionTypes = ['single', 'multiple', 'text', 'rating'];

        foreach ($quizzes as $quiz) {
            // Создаем 5-10 вопросов для каждого квиза
            $questionCount = rand(5, 10);

            for ($i = 1; $i <= $questionCount; $i++) {
                $type = $questionTypes[array_rand($questionTypes)];

                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'type' => $type,
                    'order' => $i,
                    'points' => rand(1, 5),
                    'is_required' => rand(0, 1),
                    'max_choices' => $type === 'multiple' ? rand(2, 4) : null,
                    'min_rating' => $type === 'rating' ? 1 : null,
                    'max_rating' => $type === 'rating' ? rand(5, 10) : null,
                ]);

                // Создаем переводы для вопроса
                foreach (['en', 'ru', 'ka'] as $locale) {
                    \App\Models\QuestionTranslation::create([
                        'question_id' => $question->id,
                        'locale' => $locale,
                        'content' => $this->generateQuestionContent($locale, $i)
                    ]);
                }
            }
        }

        $this->command->info('Questions seeded successfully.');
    }

    private function generateQuestionContent(string $locale, int $number): string
    {
        $questions = match($locale) {
            'en' => [
                "What is the main purpose of version control systems?",
                "Does Python support object-oriented programming?",
                "What are the basic principles of OOP?",
                "What does ACID stand for in databases?",
                "How does JavaScript handle asynchronous operations?",
                "What is the main advantage of using MVC architecture?",
                "What is the difference between HTTP and HTTPS?",
                "What is responsive web design?",
                "Why is code documentation important?",
                "What is the purpose of database indexing?"
            ],
            'ru' => [
                "Какова основная цель систем контроля версий?",
                "Поддерживает ли Python объектно-ориентированное программирование?",
                "Каковы основные принципы ООП?",
                "Что означает ACID в базах данных?",
                "Как JavaScript обрабатывает асинхронные операции?",
                "В чем основное преимущество архитектуры MVC?",
                "В чем разница между HTTP и HTTPS?",
                "Что такое адаптивный веб-дизайн?",
                "Почему важна документация кода?",
                "Какова цель индексации базы данных?"
            ],
            'ka' => [
                "რა არის ვერსიების კონტროლის სისტემების მთავარი მიზანი?",
                "უჭერს თუ არა Python-ს ობიექტზე-ორიენტირებულ პროგრამირებას?",
                "რა არის OOP-ის ძირითადი პრინციპები?",
                "რას ნიშნავს ACID მონაცემთა ბაზებში?",
                "როგორ ამუშავებს JavaScript ასინქრონულ ოპერაციებს?",
                "რა არის MVC არქიტექტურის მთავარი უპირატესობა?",
                "რა განსხვავებაა HTTP-სა და HTTPS-ს შორის?",
                "რა არის რესპონსივი ვებ-დიზაინი?",
                "რატომ არის მნიშვნელოვანი კოდის დოკუმენტაცია?",
                "რა არის მონაცემთა ბაზის ინდექსირების მიზანი?"
            ]
        };

        return $questions[array_rand($questions)] . " (#{$number})";
    }
}
