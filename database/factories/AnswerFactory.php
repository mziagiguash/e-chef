<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\QuestionAnswer;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\User;

class AnswerFactory extends Factory
{
    protected $model = QuestionAnswer::class;

    public function definition()
    {
        return [
            'attempt_id' => QuizAttempt::factory(),
            'question_id' => Question::factory(),
            'user_id' => User::factory(),
            'option_id' => null,
            'text_answer' => $this->faker->sentence(3),
            'rating_answer' => null,
            'is_correct' => $this->faker->boolean(30),
            'points_earned' => $this->faker->numberBetween(0, 5),
        ];
    }

    public function correct()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => true,
                'points_earned' => $this->faker->numberBetween(3, 5),
            ];
        });
    }

    public function incorrect()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => false,
                'points_earned' => 0,
            ];
        });
    }

    public function withOption()
    {
        return $this->state(function (array $attributes) {
            return [
                'option_id' => \App\Models\Option::factory(),
                'text_answer' => null,
            ];
        });
    }

    public function withTextAnswer()
    {
        return $this->state(function (array $attributes) {
            return [
                'option_id' => null,
                'text_answer' => $this->faker->sentence(6),
            ];
        });
    }
}
