<?php

namespace Database\Factories;

use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class InstructorFactory extends Factory
{
    protected $model = Instructor::class;

    public function definition()
    {
        return [
            'contact' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'role_id' => 3, // Предполагаем, что role_id 3 - это instructor
            'image' => $this->faker->imageUrl(),
            'status' => true,
            'password' => Hash::make('password'),
            'language' => 'en',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Instructor $instructor) {
            // Создаем переводы для всех языков
            foreach (['en', 'ru', 'ka'] as $locale) {
                \App\Models\InstructorTranslation::create([
                    'instructor_id' => $instructor->id,
                    'locale' => $locale,
                    'name' => $this->faker->name . " ({$locale})",
                    'bio' => $this->faker->paragraph . " ({$locale})",
                    'title' => $this->faker->jobTitle . " ({$locale})",
                    'designation' => $this->faker->company . " ({$locale})",
                ]);
            }
        });
    }
}
