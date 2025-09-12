<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SimpleQuizSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Очищаем таблицы в правильном порядке
        DB::table('options_translations')->truncate();
        DB::table('options')->truncate();
        DB::table('questions_translations')->truncate();
        DB::table('questions')->truncate();
        DB::table('quizzes_translations')->truncate();
        DB::table('quizzes')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🗑️  Cleared all quiz tables');

        // Доступные локали
        $locales = [
            'en' => [
                'quiz_title' => 'Quiz ',
                'quiz_desc' => 'English description for quiz ',
                'question' => 'Question ',
                'option' => 'Option ',
                'correct' => ' (Correct)'
            ],
            'ru' => [
                'quiz_title' => 'Квиз ',
                'quiz_desc' => 'Русское описание для квиза ',
                'question' => 'Вопрос ',
                'option' => 'Вариант ',
                'correct' => ' (Правильный)'
            ],
            'ka' => [
                'quiz_title' => 'ვიქტორინა ',
                'quiz_desc' => 'ქართული აღწერა ვიქტორინისთვის ',
                'question' => 'კითხვა ',
                'option' => 'ვარიანტი ',
                'correct' => ' (სწორი)'
            ]
        ];

        // 1. Создаем квизы
        $quizIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $quizData = [
                'lesson_id' => null,
                'order' => $i,
                'is_active' => 1,
                'time_limit' => 300,
                'passing_score' => 70,
                'max_attempts' => 3,
                'title' => 'Quiz ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ];

            $quizId = DB::table('quizzes')->insertGetId($quizData);
            $quizIds[] = $quizId;

            // Переводы для квиза для всех языков
            $quizTranslations = [];
            foreach ($locales as $locale => $texts) {
                $quizTranslations[] = [
                    'quiz_id' => $quizId,
                    'locale' => $locale,
                    'title' => $texts['quiz_title'] . $i,
                    'description' => $texts['quiz_desc'] . $i,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            DB::table('quizzes_translations')->insert($quizTranslations);
        }

        $this->command->info('✅ Quizzes created: ' . count($quizIds));

        // 2. Создаем вопросы
        $questionIds = [];
        foreach ($quizIds as $quizId) {
            for ($j = 1; $j <= 3; $j++) {
                $questionData = [
                    'quiz_id' => $quizId,
                    'type' => 'single',
                    'order' => $j,
                    'points' => 1,
                    'is_required' => 1,
                    'max_choices' => null,
                    'min_rating' => null,
                    'max_rating' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null
                ];

                $questionId = DB::table('questions')->insertGetId($questionData);
                $questionIds[] = $questionId;

                // Переводы для вопроса для всех языков
                $questionTranslations = [];
                foreach ($locales as $locale => $texts) {
                    $questionTranslations[] = [
                        'question_id' => $questionId,
                        'locale' => $locale,
                        'content' => $texts['question'] . $j . ' for quiz ' . $quizId . '?',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('questions_translations')->insert($questionTranslations);
            }
        }

        $this->command->info('✅ Questions created: ' . count($questionIds));

        // 3. Создаем варианты ответов
        $optionIds = [];
        foreach ($questionIds as $questionId) {
            for ($k = 1; $k <= 4; $k++) {
                $isCorrect = $k === 1; // Первый вариант - правильный

                $optionData = [
                    'question_id' => $questionId,
                    'option_text' => 'Option ' . $k . ($isCorrect ? ' (Correct)' : ''),
                    'is_correct' => $isCorrect ? 1 : 0,
                    'order' => $k,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $optionId = DB::table('options')->insertGetId($optionData);
                $optionIds[] = $optionId;

                // Переводы для варианта для всех языков
                $optionTranslations = [];
                foreach ($locales as $locale => $texts) {
                    $optionTranslations[] = [
                        'option_id' => $optionId,
                        'locale' => $locale,
                        'option_text' => $texts['option'] . $k . ($isCorrect ? $texts['correct'] : ''),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('options_translations')->insert($optionTranslations);
            }
        }

        $this->command->info('✅ Options created: ' . count($optionIds));
        $this->command->info('🎉 Simple quiz system seeded successfully with Georgian language!');

        // Показываем статистику
        $this->command->info('📊 Statistics:');
        $this->command->info('   • Quizzes: ' . DB::table('quizzes')->count());
        $this->command->info('   • Questions: ' . DB::table('questions')->count());
        $this->command->info('   • Options: ' . DB::table('options')->count());
        $this->command->info('   • Quiz translations: ' . DB::table('quizzes_translations')->count());
        $this->command->info('   • Question translations: ' . DB::table('questions_translations')->count());
        $this->command->info('   • Option translations: ' . DB::table('options_translations')->count());

        $this->command->info('🌍 Languages: English, Russian, Georgian');
    }
}
