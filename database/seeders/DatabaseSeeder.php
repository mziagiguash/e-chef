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
    CourseCategorySeeder::class,
    InstructorSeeder::class,
    CourseSeeder::class,
    QuizSeeder::class,
    LessonSeeder::class,       // ← Сначала квизы
    QuestionSeeder::class,    // ← Потом вопросы
    OptionSeeder::class,      // ← Потом опции
    MaterialSeeder::class,
    QuizAttemptSeeder::class, // ← В конце попытки
        ]);
    }
}
