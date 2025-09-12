<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseCategoryTranslation;
use App\Models\CourseCategory;

class CourseCategoryTranslationFactory extends Factory
{
    protected $model = CourseCategoryTranslation::class;

    public function definition(): array
    {
        $locale = $this->faker->randomElement(['en', 'ru', 'ka']);

        return [
            'course_category_id' => CourseCategory::factory(),
            'locale' => $locale,
            'category_name' => $this->generateCategoryName($locale),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateCategoryName(string $locale): string
    {
        $categories = match($locale) {
            'en' => [
                'Web Development',
                'Mobile Development',
                'Data Science',
                'Programming',
                'Design',
                'Artificial Intelligence',
                'Cloud Computing',
                'Cybersecurity',
                'Database Management',
                'Game Development'
            ],
            'ru' => [
                'Веб-разработка',
                'Мобильная разработка',
                'Наука о данных',
                'Программирование',
                'Дизайн',
                'Искусственный интеллект',
                'Облачные вычисления',
                'Кибербезопасность',
                'Управление базами данных',
                'Разработка игр'
            ],
            'ka' => [
                'ვებ-განვითარება',
                'მობილური განვითარება',
                'მონაცემთა მეცნიერება',
                'პროგრამირება',
                'დიზაინი',
                'ხელოვნური ინტელექტი',
                'ღრუბლოვანი გამოთვლები',
                'კიბერუსაფრთხოება',
                'მონაცემთა ბაზების მენეჯმენტი',
                'თამაშების განვითარება'
            ],
            default => [
                'Web Development',
                'Mobile Development',
                'Data Science'
            ]
        };

        return $this->faker->randomElement($categories);
    }

    public function forLocale(string $locale): static
    {
        return $this->state(function (array $attributes) use ($locale) {
            return [
                'locale' => $locale,
                'category_name' => $this->generateCategoryName($locale),
            ];
        });
    }

    public function forCategory(CourseCategory $category): static
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'course_category_id' => $category->id,
            ];
        });
    }
}
