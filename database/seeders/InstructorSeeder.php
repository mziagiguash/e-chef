<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instructor;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем первого инструктора
        $instructor1 = Instructor::create([
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
        $instructor1->translations()->create([
            'locale' => 'en',
            'name' => 'John Doe',
            'bio' => 'Experienced Laravel and PHP instructor with over 10 years of teaching experience.',
            'designation' => 'Senior Instructor',
            'title' => 'Web Development Expert'
        ]);

        $instructor1->translations()->create([
            'locale' => 'ru',
            'name' => 'Джон Доу',
            'bio' => 'Опытный инструктор по Laravel и PHP с более чем 10-летним опытом преподавания.',
            'designation' => 'Старший инструктор',
            'title' => 'Эксперт по веб-разработке'
        ]);

        $instructor1->translations()->create([
            'locale' => 'ka',
            'name' => 'ჯონ დო',
            'bio' => 'გამოცდილი Laravel და PHP ინსტრუქტორი 10 წელზე მეტი სამეურნეო გამოცდილებით.',
            'designation' => 'მთავარი ინსტრუქტორი',
            'title' => 'ვებ-განვითარების ექსპერტი'
        ]);

        // Создаем второго инструктора
        $instructor2 = Instructor::create([
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
        $instructor2->translations()->create([
            'locale' => 'en',
            'name' => 'Anna Smith',
            'bio' => 'Expert in Vue.js and modern frontend development with a passion for teaching.',
            'designation' => 'Frontend Mentor',
            'title' => 'JavaScript Specialist'
        ]);

        $instructor2->translations()->create([
            'locale' => 'ru',
            'name' => 'Анна Смит',
            'bio' => 'Эксперт по Vue.js и современной фронтенд-разработке с страстью к преподаванию.',
            'designation' => 'Фронтенд наставник',
            'title' => 'Специалист по JavaScript'
        ]);

        $instructor2->translations()->create([
            'locale' => 'ka',
            'name' => 'ანა სმითი',
            'bio' => 'Vue.js-ის და თანამედროვე ფრონტენდ განვითარების ექსპერტი სწავლებისადმი უდიდესი სიყვარულით.',
            'designation' => 'ფრონტენდ მენტორი',
            'title' => 'JavaScript-ის სპეციალისტი'
        ]);

        // Создаем еще несколько инструкторов через фабрику
        Instructor::factory()->count(8)->create();

        $this->command->info('✅ Instructors seeded successfully!');
        $this->command->info('📊 Total instructors: ' . Instructor::count());
    }
}
