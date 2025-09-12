<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Course, CourseTranslation,
    Instructor, InstructorTranslation,
    CourseCategory, CourseCategoryTranslation,
    Lesson, LessonTranslation,
    Quiz, QuizTranslation,
    Question, QuestionTranslation,
    Option, OptionTranslation
};

class CourseWithQuizzesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Очищаем все таблицы в правильном порядке
        OptionTranslation::truncate();
        Option::truncate();
        QuestionTranslation::truncate();
        Question::truncate();
        QuizTranslation::truncate();
        Quiz::truncate();
        DB::table('lessons_translations')->truncate();
        Lesson::truncate();
        CourseTranslation::truncate();
        Course::truncate();
        InstructorTranslation::truncate();
        Instructor::truncate();
        CourseCategoryTranslation::truncate();
        CourseCategory::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🗑️  Cleared all tables');

        // 1. Создаем категории
        $categories = CourseCategory::factory()->count(5)->create();
        $this->command->info('✅ Course categories created: ' . $categories->count());

        // 2. Создаем инструкторов
        $instructors = Instructor::factory()->count(8)->create();
        $this->command->info('✅ Instructors created: ' . $instructors->count());

        // 3. Создаем курсы
        $courses = Course::factory()->count(15)->create();
        $this->command->info('✅ Courses created: ' . $courses->count());

        // 4. Создаем уроки для курсов
        $lessons = collect();
        foreach ($courses as $course) {
            $courseLessons = Lesson::factory()
                ->count(rand(3, 8))
                ->create(['course_id' => $course->id]);

            $lessons = $lessons->merge($courseLessons);
        }
        $this->command->info('✅ Lessons created: ' . $lessons->count());

        // 5. Создаем квизы для уроков
        $quizzes = collect();
        foreach ($lessons as $lesson) {
            // 70% вероятность создать квиз для урока
            if (rand(1, 100) <= 70) {
                $quiz = Quiz::create([
                    'lesson_id' => $lesson->id,
                    'order' => 1,
                    'is_active' => true,
                    'time_limit' => 300,
                    'passing_score' => 70,
                    'max_attempts' => 3,
                    'title' => 'Quiz for Lesson ' . $lesson->id,
                ]);

                // Создаем переводы для квиза
                QuizTranslation::create([
                    'quiz_id' => $quiz->id,
                    'locale' => 'en',
                    'title' => 'Quiz: Lesson ' . $lesson->id,
                    'description' => 'Test your knowledge about this lesson',
                ]);

                QuizTranslation::create([
                    'quiz_id' => $quiz->id,
                    'locale' => 'ru',
                    'title' => 'Квиз: Урок ' . $lesson->id,
                    'description' => 'Проверьте свои знания по этому уроку',
                ]);

                $quizzes->push($quiz);

                // Обновляем урок с ссылкой на квиз
                $lesson->update(['quiz_id' => $quiz->id]);

                // 6. Создаем вопросы для квиза
                $questionCount = rand(5, 10);
                for ($i = 1; $i <= $questionCount; $i++) {
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'type' => 'single',
                        'order' => $i,
                        'points' => rand(1, 5),
                        'is_required' => true,
                        'max_choices' => null,
                        'min_rating' => null,
                        'max_rating' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Переводы для вопроса
                    QuestionTranslation::create([
                        'question_id' => $question->id,
                        'locale' => 'en',
                        'content' => 'Question ' . $i . ' for quiz ' . $quiz->id . '?',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    QuestionTranslation::create([
                        'question_id' => $question->id,
                        'locale' => 'ru',
                        'content' => 'Вопрос ' . $i . ' для квиза ' . $quiz->id . '?',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 7. Создаем варианты ответов для вопроса
                    $optionCount = rand(3, 5);
                    for ($j = 1; $j <= $optionCount; $j++) {
                        $isCorrect = $j === 1; // первый вариант правильный
                        $optionText = 'Option ' . $j . ($isCorrect ? ' (Correct)' : '');

                        $option = Option::create([
                            'question_id' => $question->id,
                            'text' => $optionText,
                            'is_correct' => $isCorrect,
                            'order' => $j,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Переводы для варианта
                        OptionTranslation::create([
                            'option_id' => $option->id,
                            'locale' => 'en',
                            'text' => $optionText,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        OptionTranslation::create([
                            'option_id' => $option->id,
                            'locale' => 'ru',
                            'text' => 'Вариант ' . $j . ($isCorrect ? ' (Правильный)' : ''),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        $this->command->info('✅ Quizzes created: ' . $quizzes->count());
        $this->command->info('✅ Questions created: ' . Question::count());

        // Используем прямой запрос вместо модели для подсчета options
        $optionsCount = DB::table('options')->count();
        $this->command->info('✅ Options created: ' . $optionsCount);

        $this->command->info('🎉 Course system with quizzes seeded successfully!');
    }
}
