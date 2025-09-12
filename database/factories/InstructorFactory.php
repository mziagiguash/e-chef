<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Instructor;
use Illuminate\Support\Facades\Hash;

class InstructorFactory extends Factory
{
    protected $model = Instructor::class;

    public function definition()
    {
        return [
            'contact' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'role_id' => 2, // Роль инструктора
            'image' => $this->faker->imageUrl(200, 200, 'people', true),
            'status' => $this->faker->numberBetween(0, 1),
            'password' => Hash::make('password123'),
            'language' => $this->faker->randomElement(['en', 'ru', 'ka']),
            'access_block' => $this->faker->numberBetween(0, 1),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Instructor $instructor) {
            // Создаем переводы для инструктора
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                $instructor->translations()->create([
                    'locale' => $locale,
                    'name' => $this->generateName($locale),
                    'bio' => $this->generateBio($locale),
                    'designation' => $this->generateDesignation($locale),
                    'title' => $this->generateTitle($locale)
                ]);
            }
        });
    }

    private function generateName(string $locale): string
    {
        return match($locale) {
            'en' => $this->faker->name,
            'ru' => $this->generateRussianName(),
            'ka' => $this->generateGeorgianName(),
            default => $this->faker->name
        };
    }

    private function generateRussianName(): string
    {
        $firstNames = ['Александр', 'Дмитрий', 'Михаил', 'Сергей', 'Андрей', 'Алексей', 'Екатерина', 'Мария', 'Анна', 'Ольга'];
        $lastNames = ['Иванов', 'Петров', 'Сидоров', 'Смирнов', 'Кузнецов', 'Попов', 'Васильев', 'Соколов', 'Михайлов', 'Новиков'];

        return $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames);
    }

    private function generateGeorgianName(): string
    {
        $firstNames = ['გიორგი', 'დავით', 'ნიკოლოზ', 'ლევან', 'ირაკლი', 'თამაზ', 'მარიამ', 'ნინო', 'ანა', 'თამარ'];
        $lastNames = ['ბერიძე', 'ხომერიკი', 'გელაშვილი', 'მაისურაძე', 'ჩიხრაძე', 'გაბრიჭიძე', 'მიქაბერიძე', 'ჯანაშია', 'წერეთელი', 'აბაშიძე'];

        return $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames);
    }

    private function generateBio(string $locale): string
    {
        return match($locale) {
            'en' => $this->faker->paragraph(3),
            'ru' => $this->faker->paragraph(3) . ' Опытный специалист в своей области.',
            'ka' => $this->faker->paragraph(3) . ' გამოცდილი სპეციალისტი თავის სფეროში.',
            default => $this->faker->paragraph(3)
        };
    }

    private function generateDesignation(string $locale): string
    {
        $designations = match($locale) {
            'en' => ['Senior Developer', 'Lead Instructor', 'Software Engineer', 'Data Scientist', 'UX Designer'],
            'ru' => ['Старший разработчик', 'Ведущий инструктор', 'Инженер-программист', 'Специалист по данным', 'UX-дизайнер'],
            'ka' => ['მთავარი დეველოპერი', 'ლიდერი ინსტრუქტორი', 'პროგრამისტი', 'მონაცემთა მეცნიერი', 'UX დიზაინერი'],
            default => ['Developer', 'Instructor', 'Engineer']
        };

        return $this->faker->randomElement($designations);
    }

    private function generateTitle(string $locale): string
    {
        $titles = match($locale) {
            'en' => ['Expert in Web Development', 'Mobile App Specialist', 'Data Analytics Professional', 'Cloud Architect'],
            'ru' => ['Эксперт по веб-разработке', 'Специалист по мобильным приложениям', 'Профессионал в анализе данных', 'Облачный архитектор'],
            'ka' => ['ვებ-განვითარების ექსპერტი', 'მობილური აპლიკაციების სპეციალისტი', 'მონაცემთა ანალიზის პროფესიონალი', 'ღრუბლოვანი არქიტექტორი'],
            default => ['Technology Expert', 'Development Specialist']
        };

        return $this->faker->randomElement($titles);
    }
}
