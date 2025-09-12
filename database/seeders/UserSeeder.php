<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем администратора
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'contact' => '+1 555-000-0001',
            'role_id' => 1, // admin
            'password' => Hash::make('password123'),
            'language' => 'en',
            'image' => 'users/admin.jpg',
            'full_access' => true,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Создаем инструкторов
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'contact' => '+1 555-000-0002',
            'role_id' => 2, // instructor
            'password' => Hash::make('password123'),
            'language' => 'en',
            'image' => 'users/john_doe.jpg',
            'full_access' => false,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'contact' => '+1 555-000-0003',
            'role_id' => 2, // instructor
            'password' => Hash::make('password123'),
            'language' => 'en',
            'image' => 'users/jane_smith.jpg',
            'full_access' => false,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Создаем обычных пользователей
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@example.com',
                'contact' => '+1 555-100-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'role_id' => 4, // student
                'password' => Hash::make('password123'),
                'language' => 'en',
                'image' => 'users/student' . $i . '.jpg',
                'full_access' => false,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Users seeded successfully!');
    }
}
