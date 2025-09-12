<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'contact' => $this->faker->phoneNumber, // Добавлено значение для contact
            'role_id' => 4, // или другой role_id по умолчанию
            'password' => Hash::make('password123'),
            'language' => 'en',
            'image' => $this->faker->imageUrl(200, 200, 'people'),
            'full_access' => false,
            'status' => 1,
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
