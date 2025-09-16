<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition()
    {
        return [
            'question_id' => Question::factory(),
            'is_correct' => $this->faker->boolean(30),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }

    // УДАЛЯЕМ метод configure() - переводы будем создавать в сидере
}
