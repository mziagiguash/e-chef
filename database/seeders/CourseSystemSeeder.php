<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{
    CourseTranslation,
    Course,
    InstructorTranslation,
    Instructor,
    CourseCategoryTranslation,
    CourseCategory,
    Lesson,
    LessonTranslation,
    Quiz,
    QuizTranslation,
    Question,
    QuestionTranslation,
    Option,
    OptionTranslation,
    QuestionAnswer,
    QuizAttempt
};

class CourseSystemSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Очищаем все таблицы в правильном порядке (сначала дочерние, потом родительские)
        QuestionAnswer::truncate();
        OptionTranslation::truncate();
        Option::truncate();
        QuestionTranslation::truncate();
        Question::truncate();
        QuizAttempt::truncate();
        QuizTranslation::truncate();
        Quiz::truncate();
        LessonTranslation::truncate();
        Lesson::truncate();
        CourseTranslation::truncate();
        Course::truncate();
        InstructorTranslation::truncate();
        Instructor::truncate();
        CourseCategoryTranslation::truncate();
        CourseCategory::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Создаем категории
        $categories = CourseCategory::factory()->count(5)->create();
        $this->command->info('✅ Course categories created: ' . $categories->count());

        // Создаем инструкторов
        $instructors = Instructor::factory()->count(8)->create();
        $this->command->info('✅ Instructors created: ' . $instructors->count());

        // Создаем курсы
        $courses = Course::factory()->count(15)->create();
        $this->command->info('✅ Courses created: ' . $courses->count());

        // Создаем уроки для курсов
        $lessons = collect();
        foreach ($courses as $course) {
            $courseLessons = Lesson::factory()
                ->count(rand(3, 8))
                ->create(['course_id' => $course->id]);

            $lessons = $lessons->merge($courseLessons);
        }
        $this->command->info('✅ Lessons created: ' . $lessons->count());

        // Создаем квизы для уроков
        $quizzes = collect();
        foreach ($lessons as $lesson) {
            // Не для каждого урока создаем квиз (70% вероятность)
            if (rand(1, 100) <= 70) {
                $quiz = Quiz::create([
                    'lesson_id' => $lesson->id,
                    'order' => rand(1, 5),
                    'is_active' => true,
                    'time_limit' => rand(15, 60),
                    'passing_score' => rand(60, 80),
                    'max_attempts' => rand(1, 5),
                    'title' => 'Quiz for Lesson ' . $lesson->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Создаем переводы для квиза на трех языках
                $locales = ['en', 'ru', 'ka'];
                foreach ($locales as $locale) {
                    QuizTranslation::create([
                        'quiz_id' => $quiz->id,
                        'locale' => $locale,
                        'title' => "Quiz for Lesson {$lesson->id} ({$locale})",
                        'description' => "Test your knowledge about lesson {$lesson->id} in {$locale}",
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $quizzes->push($quiz);
            }
        }
        $this->command->info('✅ Quizzes created: ' . $quizzes->count());

        // Создаем вопросы для квизов
        $questions = collect();
        foreach ($quizzes as $quiz) {
            $questionCount = rand(5, 12);
            for ($i = 1; $i <= $questionCount; $i++) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'order' => $i,
                    'type' => 'multiple_choice',
                    'points' => rand(1, 5),
                    'is_required' => rand(0, 1),
                    'max_choices' => 1,
                    'min_rating' => 1,
                    'max_rating' => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Создаем переводы для вопроса на трех языках
                foreach (['en', 'ru', 'ka'] as $locale) {
                    QuestionTranslation::create([
                        'question_id' => $question->id,
                        'locale' => $locale,
                        'content' => "Question {$i} for Quiz {$quiz->id} ({$locale})",
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $questions->push($question);
            }
        }
        $this->command->info('✅ Questions created: ' . $questions->count());

        // Создаем варианты ответов (options) и ответы (answers)
        $options = collect();
        $answers = collect();

        foreach ($questions as $question) {
            // Создаем варианты ответов (options)
            $optionCount = rand(2, 5);
            $questionOptions = [];

            for ($i = 0; $i < $optionCount; $i++) {
                $option = Option::create([
                    'question_id' => $question->id,
                    'option_text' => "Option " . ($i + 1) . " for Question {$question->id}",
                    'order' => $i + 1,
                    'is_correct' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Создаем переводы для варианта ответа на трех языках
                foreach (['en', 'ru', 'ka'] as $locale) {
                    OptionTranslation::create([
                        'option_id' => $option->id,
                        'locale' => $locale,
                        'option_text' => "Option " . ($i + 1) . " for Question {$question->id} ({$locale})",
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $questionOptions[] = $option;
                $options->push($option);
            }

            // Выбираем один правильный вариант
            $correctOptionIndex = rand(0, count($questionOptions) - 1);

            foreach ($questionOptions as $index => $option) {
                $isCorrect = ($index === $correctOptionIndex);
                $option->update(['is_correct' => $isCorrect]);

                // Создаем ответ (answer)
                $answer = QuestionAnswer::create([
                    'question_id' => $question->id,
                    'option_id' => $option->id,
                    'is_correct' => $isCorrect,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $answers->push($answer);
            }
        }

        $this->command->info('✅ Options created: ' . $options->count());
        $this->command->info('✅ Answers created: ' . $answers->count());

        $this->command->info('🎉 Course system with quizzes seeded successfully!');
        $this->command->info('📊 Statistics:');
        $this->command->info('   • Courses: ' . Course::count());
        $this->command->info('   • Lessons: ' . Lesson::count());
        $this->command->info('   • Quizzes: ' . Quiz::count());
        $this->command->info('   • Questions: ' . Question::count());
        $this->command->info('   • Options: ' . Option::count());
        $this->command->info('   • Answers: ' . QuestionAnswer::count());
    }
}
