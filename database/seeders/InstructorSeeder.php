<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instructor;
use App\Models\InstructorTranslation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InstructorSeeder extends Seeder
{
    public function run()
    {
        // Очистить таблицы перед сидированием
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        InstructorTranslation::truncate();
        Instructor::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Временное решение без Faker
        $instructors = [
            [
                'contact' => '+1-555-0101',
                'email' => 'john.smith@example.com',
                'role_id' => 3,
                'image' => null,
                'status' => true,
                'password' => Hash::make('password'),
                'language' => 'en',
            ],
            [
                'contact' => '+1-555-0102',
                'email' => 'sarah.johnson@example.com',
                'role_id' => 3,
                'image' => null,
                'status' => true,
                'password' => Hash::make('password'),
                'language' => 'en',
            ],
            [
                'contact' => '+1-555-0103',
                'email' => 'mike.wilson@example.com',
                'role_id' => 3,
                'image' => null,
                'status' => true,
                'password' => Hash::make('password'),
                'language' => 'en',
            ]
        ];

        foreach ($instructors as $instructorData) {
            $instructor = Instructor::create($instructorData);

            // Создаем переводы
            $locales = ['en', 'ru', 'ka'];
            $names = [
                'en' => ['John Smith', 'Sarah Johnson', 'Mike Wilson'],
                'ru' => ['Джон Смит', 'Сара Джонсон', 'Майк Уилсон'],
                'ka' => ['ჯონ სმიტი', 'სარა ჯონსონი', 'მაიკ ვილსონი']
            ];

            foreach ($locales as $index => $locale) {
                InstructorTranslation::create([
                    'instructor_id' => $instructor->id,
                    'locale' => $locale,
                    'name' => $names[$locale][$index] ?? $names['en'][$index],
                    'bio' => "Experienced instructor in {$locale}",
                    'title' => "Senior Instructor ({$locale})",
                    'designation' => "Tech Academy ({$locale})",
                ]);
            }
        }
    }
}
