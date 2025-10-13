<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Quiz;
use App\Models\Lesson;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => $this->faker->words(3, true), // Основное название
            'questions_count' => $this->faker->numberBetween(5, 15),
            'time_limit' => $this->faker->numberBetween(10, 60),
            'passing_score' => $this->faker->numberBetween(60, 80),
            'max_attempts' => $this->faker->numberBetween(1, 3),
            'is_active' => $this->faker->boolean(80),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Quiz $quiz) {
            // Создаем переводы для всех локалей
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                \App\Models\QuizTranslation::factory()
                    ->create([
                        'quiz_id' => $quiz->id,
                        'locale' => $locale,
                    ]);
            }
        });
    }

    public function forLesson(Lesson $lesson): static
    {
        return $this->state(function (array $attributes) use ($lesson) {
            return [
                'lesson_id' => $lesson->id,
            ];
        });
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    public function withTimeLimit(int $minutes): static
    {
        return $this->state(function (array $attributes) use ($minutes) {
            return [
                'time_limit' => $minutes,
            ];
        });
    }

    public function withPassingScore(int $score): static
    {
        return $this->state(function (array $attributes) use ($score) {
            return [
                'passing_score' => $score,
            ];
        });
    }

    public function withQuestionsCount(int $count): static
    {
        return $this->state(function (array $attributes) use ($count) {
            return [
                'questions_count' => $count,
            ];
        });
    }
}
