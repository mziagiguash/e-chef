<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CourseCategory;
use App\Models\CourseCategoryTranslation;

class CourseCategorySeeder extends Seeder
{
    public function run()
    {
        // Отключаем проверку внешних ключей
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Очищаем таблицы в правильном порядке
        CourseCategoryTranslation::truncate();
        CourseCategory::truncate();

        // Включаем проверку внешних ключей обратно
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Основные категории: Design, Development, Business, IT & Software
        $mainCategories = [
            [
                'en' => 'Design',
                'ru' => 'Дизайн',
                'ka' => 'დიზაინი'
            ],
            [
                'en' => 'Development',
                'ru' => 'Разработка',
                'ka' => 'განვითარება'
            ],
            [
                'en' => 'Business',
                'ru' => 'Бизнес',
                'ka' => 'ბიზნესი'
            ],
            [
                'en' => 'IT & Software',
                'ru' => 'IT и Программное обеспечение',
                'ka' => 'IT და პროგრამული უზრუნველყოფა'
            ]
        ];

        foreach ($mainCategories as $categoryNames) {
            // Создаем категорию
            $category = CourseCategory::create([
                'category_status' => 1, // активная
                'category_image' => $this->generateCategoryImage($categoryNames['en']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Создаем переводы для всех языков
            foreach (['en', 'ru', 'ka'] as $locale) {
                CourseCategoryTranslation::create([
                    'course_category_id' => $category->id,
                    'locale' => $locale,
                    'category_name' => $categoryNames[$locale],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Course categories seeded successfully!');
        $this->command->info('📊 Total categories: ' . CourseCategory::count());
        $this->command->info('🌍 Total translations: ' . CourseCategoryTranslation::count());

        // Выводим список созданных категорий
        $categories = CourseCategory::with('translations')->get();
        foreach ($categories as $category) {
            $this->command->info("📁 Category ID: {$category->id}");
            foreach ($category->translations as $translation) {
                $this->command->info("   {$translation->locale}: {$translation->category_name}");
            }
        }
    }

    private function generateCategoryImage(string $categoryName): string
    {
        $imageMap = [
            'Design' => 'category-design.jpg',
            'Development' => 'category-development.jpg',
            'Business' => 'category-business.jpg',
            'IT & Software' => 'category-it-software.jpg',
            'Дизайн' => 'category-design.jpg',
            'Разработка' => 'category-development.jpg',
            'Бизнес' => 'category-business.jpg',
            'IT и Программное обеспечение' => 'category-it-software.jpg',
            'დიზაინი' => 'category-design.jpg',
            'განვითარება' => 'category-development.jpg',
            'ბიზნესი' => 'category-business.jpg',
            'IT და პროგრამული უზრუნველყოფა' => 'category-it-software.jpg'
        ];

        return $imageMap[$categoryName] ?? 'category-default.jpg';
    }
}
