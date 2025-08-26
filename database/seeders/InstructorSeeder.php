<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instructor;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Instructor::create([
            'name' => json_encode([
                'en' => 'John Doe',
                'ru' => 'Джон Доу',
            ]),
            'email' => 'john.doe@example.com',
            'designation' => 'Senior Instructor',
            'bio' => 'Experienced Laravel and PHP instructor.',
            'image' => 'instructors/john_doe.jpg',
        ]);

        Instructor::create([
            'name' => json_encode([
                'en' => 'Anna Smith',
                'ru' => 'Анна Смит',
            ]),
            'email' => 'anna.smith@example.com',
            'designation' => 'Frontend Mentor',
            'bio' => 'Expert in Vue.js and modern frontend development.',
            'instructor_image' => 'instructors/anna_smith.jpg',
        ]);
    }
}
