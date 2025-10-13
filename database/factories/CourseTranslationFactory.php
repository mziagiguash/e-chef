<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseTranslation;
use App\Models\Course;

class CourseTranslationFactory extends Factory
{
    protected $model = CourseTranslation::class;

    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'ru', 'ka']);

        return [
            'course_id' => Course::factory(),
            'locale' => $locale,
            'title' => $this->generateTitle($locale),
            'description' => $this->generateDescription($locale),
            'prerequisites' => $this->generatePrerequisites($locale),
            'keywords' => $this->generateKeywords($locale),
        ];
    }

    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => [
                'Complete Web Development Bootcamp',
                'Advanced Python Programming',
                'Data Science Masterclass',
                'Mobile App Development with Flutter',
                'UX/UI Design Fundamentals'
            ],
            'ru' => [
                'Полный курс веб-разработки',
                'Продвинутое программирование на Python',
                'Мастер-класс по Data Science',
                'Разработка мобильных приложений на Flutter',
                'Основы UX/UI дизайна'
            ],
            'ka' => [
                'სრული ვებ-განვითარების კურსი',
                'Python-ის პროგრამირების გაფართოებული კურსი',
                'Data Science-ის მასტერკლასი',
                'მობილური აპლიკაციების განვითარება Flutter-ით',
                'UX/UI დიზაინის საფუძვლები'
            ]
        };

        return $this->faker->randomElement($titles);
    }

    private function generateDescription(string $locale): string
    {
        return match($locale) {
            'en' => $this->faker->paragraphs(3, true),
            'ru' => $this->faker->paragraphs(3, true),
            'ka' => $this->faker->paragraphs(3, true)
        };
    }

    private function generatePrerequisites(string $locale): string
    {
        return match($locale) {
            'en' => 'Basic programming knowledge. Familiarity with ' . $this->faker->word() . '.',
            'ru' => 'Базовые знания программирования. Знакомство с ' . $this->faker->word() . '.',
            'ka' => 'პროგრამირების საბაზისო ცოდნა. გაეცანით ' . $this->faker->word() . '.'
        };
    }

    private function generateKeywords(string $locale): string
    {
        $keywords = match($locale) {
            'en' => ['programming', 'web development', 'coding', 'technology', 'learning'],
            'ru' => ['программирование', 'веб-разработка', 'кодирование', 'технологии', 'обучение'],
            'ka' => ['პროგრამირება', 'ვებ-განვითარება', 'კოდირება', 'ტექნოლოგიები', 'სწავლა']
        };

        return implode(', ', $this->faker->randomElements($keywords, 3));
    }
}
