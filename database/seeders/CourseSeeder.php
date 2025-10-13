<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Создаем 15 активных курсов
        Course::factory(15)->create(['status' => 2])->each(function ($course) {
            // Создаем переводы для каждого языка
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                $course->translations()->create([
                    'locale' => $locale,
                    'title' => $this->generateTitle($locale),
                    'description' => $this->generateDescription($locale),
                    'prerequisites' => $this->generatePrerequisites($locale),
                    'keywords' => $this->generateKeywords($locale),
                ]);
            }
        });

        // Создаем 5 неактивных курсов
        Course::factory(5)->create(['status' => 1]);
    }

    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => [
                'Complete Web Development Bootcamp',
                'Advanced Python Programming',
                'Data Science Masterclass',
                'Mobile App Development with Flutter',
                'UX/UI Design Fundamentals',
                'JavaScript Frameworks Guide',
                'Cloud Computing Essentials',
                'Machine Learning Basics',
                'Database Design Principles',
                'DevOps for Beginners'
            ],
            'ru' => [
                'Полный курс веб-разработки',
                'Продвинутое программирование на Python',
                'Мастер-класс по Data Science',
                'Разработка мобильных приложений на Flutter',
                'Основы UX/UI дизайна',
                'Руководство по JavaScript фреймворкам',
                'Основы облачных вычислений',
                'Основы машинного обучения',
                'Принципы проектирования баз данных',
                'DevOps для начинающих'
            ],
            'ka' => [
                'სრული ვებ-განვითარების კურსი',
                'Python-ის პროგრამირების გაფართოებული კურსი',
                'Data Science-ის მასტერკლასი',
                'მობილური აპლიკაციების განვითარება Flutter-ით',
                'UX/UI დიზაინის საფუძვლები',
                'JavaScript ფრეიმვორქების გზამკვლევი',
                'ღრუბლოვანი კომპიუტინგის საფუძვლები',
                'მანქანური სწავლების საფუძვლები',
                'მონაცემთა ბაზების დიზაინის პრინციპები',
                'DevOps დამწყებთათვის'
            ]
        };

        return $titles[array_rand($titles)];
    }

    private function generateDescription(string $locale): string
    {
        $base = "This comprehensive course will teach you everything you need to know about ";
        $topics = ['web development', 'programming', 'data science', 'mobile development', 'design'];

        return match($locale) {
            'en' => $base . $topics[array_rand($topics)] . ". Learn from industry experts with hands-on projects.",
            'ru' => "Этот комплексный курс научит вас всему, что нужно знать о " . $topics[array_rand($topics)] . ". Учитесь у экспертов отрасли с практическими проектами.",
            'ka' => "ეს ყოვლისმომცველი კურსი გასწავლით ყველაფერს, რაც თქვენ უნდა იცოდეთ " . $topics[array_rand($topics)] . ". ისწავლეთ ინდუსტრიის ექსპერტებისგან პრაქტიკული პროექტებით."
        };
    }

    private function generatePrerequisites(string $locale): string
    {
        return match($locale) {
            'en' => 'Basic programming knowledge recommended. No prior experience required.',
            'ru' => 'Рекомендуются базовые знания программирования. Предварительный опыт не требуется.',
            'ka' => 'რეკომენდირებულია პროგრამირების საბაზისო ცოდნა. წინასწარი გამოცდილება არ არის საჭირო.'
        };
    }

    private function generateKeywords(string $locale): string
    {
        $keywords = match($locale) {
            'en' => ['programming', 'development', 'coding', 'technology', 'learning', 'education'],
            'ru' => ['программирование', 'разработка', 'кодирование', 'технологии', 'обучение', 'образование'],
            'ka' => ['პროგრამირება', 'განვითარება', 'კოდირება', 'ტექნოლოგიები', 'სწავლა', 'განათლება']
        };

        $selected = array_rand($keywords, 4);
        return implode(', ', array_map(fn($index) => $keywords[$index], $selected));
    }
}
