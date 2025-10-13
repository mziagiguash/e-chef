<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\QuizTranslation;
use App\Models\Quiz;

class QuizTranslationFactory extends Factory
{
    protected $model = QuizTranslation::class;

    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'ru', 'ka']);

        return [
            'quiz_id' => Quiz::factory(),
            'locale' => $locale,
            'title' => $this->generateTitle($locale),
            'description' => $this->generateDescription($locale),
        ];
    }

    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => [
                'Basic Concepts Quiz',
                'Advanced Topics Test',
                'Knowledge Check',
                'Skills Assessment',
                'Progress Evaluation',
                'Understanding Test',
                'Practice Quiz',
                'Final Assessment',
                'Module Review',
                'Comprehension Check'
            ],
            'ru' => [
                'Тест по основным понятиям',
                'Тест по продвинутым темам',
                'Проверка знаний',
                'Оценка навыков',
                'Оценка прогресса',
                'Тест на понимание',
                'Практический тест',
                'Финальная оценка',
                'Обзор модуля',
                'Проверка понимания'
            ],
            'ka' => [
                'ძირითადი ცნებების ტესტი',
                'მოწინავე თემების ტესტი',
                'ცოდნის შემოწმება',
                'უნარების შეფასება',
                'პროგრესის შეფასება',
                'გაგების ტესტი',
                'პრაქტიკული ტესტი',
                'საბოლოო შეფასება',
                'მოდულის მიმოხილვა',
                'გაგების შემოწმება'
            ]
        };

        return $titles[array_rand($titles)];
    }

    private function generateDescription(string $locale): string
    {
        return match($locale) {
            'en' => 'Test your understanding of the lesson material with this comprehensive quiz.',
            'ru' => 'Проверьте свое понимание материала урока с помощью этого комплексного теста.',
            'ka' => 'შეამოწმეთ გაკვეთილის მასალის გაგება ამ ყოვლისმომცველი ტესტის საშუალებით.'
        };
    }

    public function english(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'en',
                'title' => $this->generateTitle('en'),
            ];
        });
    }

    public function russian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ru',
                'title' => $this->generateTitle('ru'),
            ];
        });
    }

    public function georgian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'locale' => 'ka',
                'title' => $this->generateTitle('ka'),
            ];
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
}
