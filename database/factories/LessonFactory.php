<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Quiz;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'quiz_id' => null,
            'title' => null, // ← ДОЛЖНО БЫТЬ NULL, так как переводы в отдельной таблице
            'description' => null, // ← ДОЛЖНО БЫТЬ NULL
            'notes' => null, // ← ДОЛЖНО БЫТЬ NULL
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Lesson $lesson) {
            // Создаем переводы для всех локалей
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                \App\Models\LessonTranslation::factory()
                    ->create([
                        'lesson_id' => $lesson->id,
                        'locale' => $locale,
                    ]);
            }
        });
    }

    public function forCourse(Course $course): static
    {
        return $this->state(function (array $attributes) use ($course) {
            return [
                'course_id' => $course->id,
            ];
        });
    }

    public function withRandomQuiz(array $quizIds = []): static
    {
        return $this->state(function (array $attributes) use ($quizIds) {
            if (!empty($quizIds) && $this->faker->boolean(30)) {
                return [
                    'quiz_id' => $this->faker->randomElement($quizIds),
                ];
            }
            return ['quiz_id' => null];
        });
    }

    public function withQuiz(Quiz $quiz = null): static
    {
        return $this->state(function (array $attributes) use ($quiz) {
            return [
                'quiz_id' => $quiz ? $quiz->id : Quiz::factory()->create()->id,
            ];
        });
    }

    public function withoutQuiz(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'quiz_id' => null,
            ];
        });
    }
}
