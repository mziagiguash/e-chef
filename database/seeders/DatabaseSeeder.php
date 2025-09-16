<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
   public function run()
    {
        $this->call([

    RoleSeeder::class,
    UserSeeder::class,
    InstructorSeeder::class,
    CourseCategorySeeder::class,
    InstructorSeeder::class,
    CourseSeeder::class,
    LessonSeeder::class,
    QuizSeeder::class,      // ← Сначала квизы
    QuestionSeeder::class,    // ← Потом вопросы
    OptionSeeder::class,
    MaterialSeeder::class,
    QuizAttemptSeeder::class,
    AnswerSeeder::class,
        ]);
    }
}
