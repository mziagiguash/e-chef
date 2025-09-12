<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Answer;
use App\Models\Question;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'question_id' => Question::factory(),
            'text' => $this->faker->sentence(6),
            'is_correct' => $this->faker->boolean(25), // 25% chance of being correct
            'order' => $this->faker->numberBetween(1, 10),
            'explanation' => $this->faker->boolean(50) ? $this->faker->paragraph(2) : null,
        ];
    }

    // Состояния для фабрики
    public function correct()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => true,
            ];
        });
    }

    public function incorrect()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => false,
            ];
        });
    }
}
