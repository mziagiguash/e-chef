<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseTranslation;
use App\Models\CourseCategory;
use App\Models\CourseCategoryTranslation;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Получаем существующих инструкторов из базы
        $johnDoe = Instructor::where('email', 'john.doe@example.com')->first();
        $annaSmith = Instructor::where('email', 'anna.smith@example.com')->first();

        // Если инструкторы не найдены, создаем их
        if (!$johnDoe) {
            $johnDoe = Instructor::create([
                'contact' => '+1 555-123-4567',
                'email' => 'john.doe@example.com',
                'role_id' => 2,
                'image' => 'instructors/john_doe.jpg',
                'status' => 1,
                'password' => Hash::make('password123'),
                'language' => 'en',
                'access_block' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Создаем переводы для первого инструктора
            $johnDoe->translations()->create([
                'locale' => 'en',
                'name' => 'John Doe',
                'bio' => 'Experienced Laravel and PHP instructor with over 10 years of teaching experience.',
                'designation' => 'Senior Instructor',
                'title' => 'Web Development Expert'
            ]);
        }

        if (!$annaSmith) {
            $annaSmith = Instructor::create([
                'contact' => '+1 555-987-6543',
                'email' => 'anna.smith@example.com',
                'role_id' => 2,
                'image' => 'instructors/anna_smith.jpg',
                'status' => 1,
                'password' => Hash::make('password123'),
                'language' => 'en',
                'access_block' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Создаем переводы для второго инструктора
            $annaSmith->translations()->create([
                'locale' => 'en',
                'name' => 'Anna Smith',
                'bio' => 'Expert in Vue.js and modern frontend development with a passion for teaching.',
                'designation' => 'Frontend Mentor',
                'title' => 'JavaScript Specialist'
            ]);
        }

        // Используем существующие категории вместо создания новых
        $programmingCategory = CourseCategory::whereHas('translations', function($query) {
            $query->where('category_name', 'Development');
        })->first();

        $designCategory = CourseCategory::whereHas('translations', function($query) {
            $query->where('category_name', 'Design');
        })->first();

        // Если категории не найдены (на случай если сидер категорий не был запущен)
        if (!$programmingCategory) {
            $programmingCategory = CourseCategory::create([
                'category_status' => 1,
                'category_image' => 'category-development.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Создаем переводы для категории Programming
            $this->createCategoryTranslations($programmingCategory->id, [
                'en' => ['category_name' => 'Development'],
                'ru' => ['category_name' => 'Разработка'],
                'ka' => ['category_name' => 'განვითარება']
            ]);
        }

        if (!$designCategory) {
            $designCategory = CourseCategory::create([
                'category_status' => 1,
                'category_image' => 'category-design.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Создаем переводы для категории Design
            $this->createCategoryTranslations($designCategory->id, [
                'en' => ['category_name' => 'Design'],
                'ru' => ['category_name' => 'Дизайн'],
                'ka' => ['category_name' => 'დიზაინი']
            ]);
        }

        // Первый курс - Веб-разработка
        $course1 = Course::create([
            'status' => 1, // active
            'course_category_id' => $programmingCategory->id,
            'instructor_id' => $johnDoe->id,
            'courseType' => 'paid',
            'coursePrice' => 299.99,
            'courseOldPrice' => 499.99,
            'subscription_price' => null,
            'start_from' => now()->addDays(15)->format('Y-m-d'),
            'duration' => 12,
            'lesson' => 48,
            'course_code' => 'CRS-WEB-001',
            'thumbnail_video_url' => null,
            'tag' => 'popular',
            'image' => 'courses/web-dev-course.jpg',
            'thumbnail_image' => 'courses/thumbs/web-dev-thumb.jpg',
            'thumbnail_video_file' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        // Создаем переводы для первого курса
        $this->createCourseTranslations($course1->id, [
            'en' => [
                'title' => 'Complete Web Development Bootcamp',
                'description' => 'Learn modern web development technologies including HTML, CSS, JavaScript, React, Node.js and more. Build real-world projects and become a full-stack developer.',
                'prerequisites' => 'Basic computer knowledge. No programming experience required.',
                'keywords' => 'web development, programming, javascript, react, nodejs'
            ],
            'ru' => [
                'title' => 'Полный курс веб-разработки',
                'description' => 'Изучите современные технологии веб-разработки, включая HTML, CSS, JavaScript, React, Node.js и многое другое. Создавайте реальные проекты и станьте full-разработчиком.',
                'prerequisites' => 'Базовые знания компьютера. Опыт программирования не требуется.',
                'keywords' => 'веб-разработка, программирование, javascript, react, nodejs'
            ],
            'ka' => [
                'title' => 'სრული ვებ-განვითარების კურსი',
                'description' => 'ისწავლეთ თანამედროვე ვებ-განვითარების ტექნოლოგიები, მათ შორის HTML, CSS, JavaScript, React, Node.js და სხვა. ააგეთ რეალური პროექტები და გახდეთ full-stack დეველოპერი.',
                'prerequisites' => 'კომპიუტერის საბაზისო ცოდნა. პროგრამირების გამოცდილება არ არის საჭირო.',
                'keywords' => 'ვებ-განვითარება, პროგრამირება, javascript, react, nodejs'
            ]
        ]);

        // Второй курс - UX/UI Дизайн
        $course2 = Course::create([
            'status' => 1, // active
            'course_category_id' => $designCategory->id,
            'instructor_id' => $annaSmith->id,
            'courseType' => 'subscription',
            'coursePrice' => 0.00,
            'courseOldPrice' => null,
            'subscription_price' => 29.99,
            'start_from' => now()->addDays(30)->format('Y-m-d'),
            'duration' => 8,
            'lesson' => 32,
            'course_code' => 'CRS-DESIGN-002',
            'thumbnail_video_url' => null,
            'tag' => 'featured',
            'image' => 'courses/design-course.jpg',
            'thumbnail_image' => 'courses/thumbs/design-thumb.jpg',
            'thumbnail_video_file' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        // Создаем переводы для второго курса
        $this->createCourseTranslations($course2->id, [
            'en' => [
                'title' => 'UX/UI Design Masterclass',
                'description' => 'Master the art of user experience and interface design. Learn design principles, prototyping, and user research techniques. Create stunning interfaces that users love.',
                'prerequisites' => 'Basic design understanding. Creative thinking skills. Familiarity with design software is a plus.',
                'keywords' => 'ux design, ui design, prototyping, user research, figma'
            ],
            'ru' => [
                'title' => 'Мастер-класс по UX/UI дизайну',
                'description' => 'Освойте искусство проектирования пользовательского опыта и интерфейсов. Изучите принципы дизайна, прототипирование и методы исследования пользователей. Создавайте потрясающие интерфейсы, которые нравятся пользователям.',
                'prerequisites' => 'Базовое понимание дизайна. Навыки творческого мышления. Знакомство с дизайн-программами будет плюсом.',
                'keywords' => 'ux дизайн, ui дизайн, прототипирование, исследование пользователей, figma'
            ],
            'ka' => [
                'title' => 'UX/UI დიზაინის მასტერკლასი',
                'description' => 'დაეუფლეთ მომხმარებლის გამოცდილების და ინტერფეისის დიზაინის ხელოვნებას. ისწავლეთ დიზაინის პრინციპები, პროტოტიპირება და მომხმარებლის კვლევის მეთოდები. შექმენით შესანიშნავი ინტერფეისები, რომლებიც მომხმარებლებს უყვართ.',
                'prerequisites' => 'დიზაინის საბაზისო გაგება. შემოქმედებითი აზროვნების უნარები. დიზაინის პროგრამებთან გაცნობა პლიუსი იქნება.',
                'keywords' => 'ux დიზაინი, ui დიზაინი, პროტოტიპირება, მომხმარებლის კვლევა, figma'
            ]
        ]);

        $this->command->info('2 courses with translations created successfully!');
    }

    private function createCourseTranslations(int $courseId, array $translations): void
    {
        foreach ($translations as $locale => $data) {
            CourseTranslation::create([
                'course_id' => $courseId,
                'locale' => $locale,
                'title' => $data['title'],
                'description' => $data['description'],
                'prerequisites' => $data['prerequisites'],
                'keywords' => $data['keywords'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createCategoryTranslations(int $categoryId, array $translations): void
    {
        foreach ($translations as $locale => $data) {
            CourseCategoryTranslation::create([
                'course_category_id' => $categoryId,
                'locale' => $locale,
                'category_name' => $data['category_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
