<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Создаем 50 студентов
        Student::factory(50)->create();

        // Создаем тестового студента для демонстрации
        Student::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'contact' => '+1234567890',
            'password' => Hash::make('password'), // ← Добавлен пароль
        ]);
    }
}
