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
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Генерирует заголовок квиза в зависимости от языка
     */
    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => [
                'Basic Programming Concepts',
                'Advanced Algorithms Quiz',
                'Web Development Fundamentals',
                'Database Design Test',
                'Object-Oriented Programming Assessment',
                'Frontend Frameworks Knowledge Check',
                'Backend Development Exam',
                'Testing and Debugging Quiz',
                'Deployment Strategies Test',
                'Project Configuration Assessment'
            ],
            'ru' => [
                'Основные концепции программирования',
                'Продвинутые алгоритмы - Тест',
                'Основы веб-разработки',
                'Тест по проектированию баз данных',
                'Оценка ООП',
                'Проверка знаний фронтенд фреймворков',
                'Экзамен по бэкенд разработке',
                'Тест по тестированию и отладке',
                'Тест по стратегиям развертывания',
                'Оценка конфигурации проекта'
            ],
            'ka' => [
                'პროგრამირების ძირითადი ცნებები',
                'გაფართოებული ალგორითმების ტესტი',
                'ვებ-განვითარების საფუძვლები',
                'მონაცემთა ბაზების დიზაინის ტესტი',
                'ობიექტზე-ორიენტირებული პროგრამირების შეფასება',
                'ფრონტენდ ფრეიმვორკების ცოდნის შემოწმება',
                'ბექენდ განვითარების გამოცდა',
                'ტესტირების და დებაგინგის ტესტი',
                'დეპლოიმენტის სტრატეგიების ტესტი',
                'პროექტის კონფიგურაციის შეფასება'
            ]
        };

        return $titles[array_rand($titles)];
    }

    /**
     * Генерирует описание в зависимости от языка
     */
    private function generateDescription(string $locale): string
    {
        return match($locale) {
            'en' => $this->faker->paragraph() . ' Test your knowledge and understanding of the course material.',
            'ru' => $this->faker->paragraph() . ' Проверьте свои знания и понимание материала курса.',
            'ka' => $this->faker->paragraph() . ' შეამოწმეთ თქვენი ცოდნა და კურსის მასალის გაგება.'
        };
    }

    // Состояния для конкретных локалей
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
