<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseCategory;

class CourseCategoryFactory extends Factory
{
    protected $model = CourseCategory::class;

    public function definition()
    {
        return [
            'category_status' => $this->faker->numberBetween(0, 1),
            'category_image' => $this->faker->imageUrl(400, 300, 'business', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (CourseCategory $category) {
            // Создаем переводы для категории
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                $category->translations()->create([
                    'locale' => $locale,
                    'category_name' => $this->generateCategoryName($locale)
                ]);
            }
        });
    }

    private function generateCategoryName(string $locale): string
    {
        $categories = match($locale) {
            'en' => [
                'Web Development', 'Mobile Development', 'Data Science', 'Programming', 'Design',
                'Artificial Intelligence', 'Cloud Computing', 'Cybersecurity', 'Database Management', 'Game Development'
            ],
            'ru' => [
                'Веб-разработка', 'Мобильная разработка', 'Наука о данных', 'Программирование', 'Дизайн',
                'Искусственный интеллект', 'Облачные вычисления', 'Кибербезопасность', 'Управление базами данных', 'Разработка игр'
            ],
            'ka' => [
                'ვებ-განვითარება', 'მობილური განვითარება', 'მონაცემთა მეცნიერება', 'პროგრამირება', 'დიზაინი',
                'ხელოვნური ინტელექტი', 'ღრუბლოვანი გამოთვლები', 'კიბერუსაფრთხოება', 'მონაცემთა ბაზების მენეჯმენტი', 'თამაშების განვითარება'
            ],
            default => ['Web Development', 'Mobile Development', 'Data Science']
        };

        return $this->faker->randomElement($categories);
    }
}
