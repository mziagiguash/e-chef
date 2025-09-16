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
        return [
            'quiz_id' => Quiz::factory(),
            'type' => $this->faker->randomElement([
    'single', 'multiple', 'text', 'rating' // вместо констант
            ]),
            'order' => $this->faker->numberBetween(1, 20),
            'points' => $this->faker->numberBetween(1, 5),
            'is_required' => $this->faker->boolean(80),
            'max_choices' => $this->faker->numberBetween(1, 5),
            'min_rating' => $this->faker->numberBetween(1, 3),
            'max_rating' => $this->faker->numberBetween(4, 10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Question $question) {
            // Создаем переводы для всех языков
            foreach (['en', 'ru', 'ka'] as $locale) {
                \App\Models\QuestionTranslation::create([
                    'question_id' => $question->id,
                    'locale' => $locale,
                    'content' => "Question content in {$locale} - " . $this->faker->sentence(6)
                ]);
            }

            // Для вопросов с вариантами ответов создаем options
            if ($question->isMultipleChoice()) {
                $correctCount = $question->type === Question::TYPE_SINGLE ? 1 : $this->faker->numberBetween(1, 3);

                // Создаем 3-5 вариантов ответов
                $optionCount = $this->faker->numberBetween(3, 5);

                for ($i = 1; $i <= $optionCount; $i++) {
                    $isCorrect = $i <= $correctCount;

                    $option = \App\Models\Option::create([
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'order' => $i
                    ]);

                    // Создаем переводы для option
                    foreach (['en', 'ru', 'ka'] as $locale) {
                        \App\Models\OptionTranslation::create([
                            'option_id' => $option->id,
                            'locale' => $locale,
                            'option_text' => "Option {$i} in {$locale} - " . ($isCorrect ? '[CORRECT] ' : '') . $this->faker->words(3, true)
                        ]);
                    }
                }
            }
        });
    }

    // Состояния для разных типов вопросов
public function singleChoice()
{
    return $this->state([
        'type' => 'single',
    ]);
}

public function multipleChoice()
{
    return $this->state([
        'type' => 'multiple',
    ]);
}

public function textType()
{
    return $this->state([
        'type' => 'text',
    ]);
}

public function ratingType()
{
    return $this->state([
        'type' => 'rating',
    ]);
}
}
