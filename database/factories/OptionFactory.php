<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Option;
use App\Models\Question;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'is_correct' => $this->faker->boolean(30),
            'order' => $this->faker->numberBetween(1, 10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Option $option) {
            // Создаем переводы для всех локалей
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                \App\Models\OptionTranslation::factory()
                    ->create([
                        'option_id' => $option->id,
                        'locale' => $locale,
                    ]);
            }
        });
    }

    public function forQuestion(Question $question): static
    {
        return $this->state(function (array $attributes) use ($question) {
            return [
                'question_id' => $question->id,
            ];
        });
    }

    public function correct(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => true,
            ];
        });
    }

    public function incorrect(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => false,
            ];
        });
    }

    public function withOrder(int $order): static
    {
        return $this->state(function (array $attributes) use ($order) {
            return [
                'order' => $order,
            ];
        });
    }
}
