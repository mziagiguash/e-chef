<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'contact' => 'admin@example.com',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // можешь поменять на более сложный
            'role_id' => 'superadmin',
            'instructor_id' => null, // если поле обязательно, укажи нужный ID
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
