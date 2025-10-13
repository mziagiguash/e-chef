<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        $type = $this->faker->randomElement(['single', 'multiple', 'text', 'rating']);

        return [
            'quiz_id' => Quiz::factory(),
            'type' => $type,
            'order' => $this->faker->numberBetween(1, 20),
            'points' => $this->faker->numberBetween(1, 5),
            'is_required' => $this->faker->boolean(80),
            'max_choices' => $type === 'multiple' ? $this->faker->numberBetween(2, 4) : null,
            'min_rating' => $type === 'rating' ? 1 : null,
            'max_rating' => $type === 'rating' ? 5 : null,
        ];
    }

    public function singleChoice()
    {
        return $this->state([
            'type' => 'single',
            'max_choices' => null,
            'min_rating' => null,
            'max_rating' => null,
        ]);
    }

    public function multipleChoice()
    {
        return $this->state([
            'type' => 'multiple',
            'max_choices' => $this->faker->numberBetween(2, 4),
            'min_rating' => null,
            'max_rating' => null,
        ]);
    }

    public function textType()
    {
        return $this->state([
            'type' => 'text',
            'max_choices' => null,
            'min_rating' => null,
            'max_rating' => null,
        ]);
    }

    public function ratingType()
    {
        return $this->state([
            'type' => 'rating',
            'max_choices' => null,
            'min_rating' => 1,
            'max_rating' => 5,
        ]);
    }

    public function forQuiz(Quiz $quiz)
    {
        return $this->state([
            'quiz_id' => $quiz->id,
        ]);
    }
}
