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
    InstructorSeeder::class,
    StudentSeeder::class,
    CourseCategorySeeder::class,
    CourseSeeder::class,
    LessonSeeder::class,
    QuizSeeder::class,
    QuestionSeeder::class,
    OptionSeeder::class,
    MaterialSeeder::class,
    //QuizAttemptSeeder::class,
   // AnswerSeeder::class,
        ]);
    }
}
