<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Question;
use App\Models\Quiz;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        $types = [
            Question::TYPE_SINGLE,
            Question::TYPE_MULTIPLE,
            Question::TYPE_TEXT,
            Question::TYPE_RATING
        ];

        $type = $this->faker->randomElement($types);

        return [
            'quiz_id' => Quiz::factory(),
            'type' => $type,
            'order' => $this->faker->numberBetween(1, 20),
            'points' => $this->faker->numberBetween(1, 10),
            'is_required' => $this->faker->boolean(70),
            'max_choices' => $type === Question::TYPE_MULTIPLE ? $this->faker->numberBetween(2, 5) : null,
            'min_rating' => $type === Question::TYPE_RATING ? 1 : null,
            'max_rating' => $type === Question::TYPE_RATING ? $this->faker->numberBetween(5, 10) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Question $question) {
            // Создаем переводы для всех локалей
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                \App\Models\QuestionTranslation::factory()
                    ->create([
                        'question_id' => $question->id,
                        'locale' => $locale,
                    ]);
            }

        });
    }

    public function forQuiz(Quiz $quiz): static
    {
        return $this->state(function (array $attributes) use ($quiz) {
            return [
                'quiz_id' => $quiz->id,
            ];
        });
    }

    public function singleChoice(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Question::TYPE_SINGLE,
                'max_choices' => null,
                'min_rating' => null,
                'max_rating' => null,
            ];
        });
    }

    public function multipleChoice(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Question::TYPE_MULTIPLE,
                'max_choices' => $this->faker->numberBetween(2, 5),
                'min_rating' => null,
                'max_rating' => null,
            ];
        });
    }

    public function textType(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Question::TYPE_TEXT,
                'max_choices' => null,
                'min_rating' => null,
                'max_rating' => null,
            ];
        });
    }

    public function ratingType(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Question::TYPE_RATING,
                'max_choices' => null,
                'min_rating' => 1,
                'max_rating' => $this->faker->numberBetween(5, 10),
            ];
        });
    }

    public function required(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_required' => true,
            ];
        });
    }

    public function optional(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_required' => false,
            ];
        });
    }
}
