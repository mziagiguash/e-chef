<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MaterialTranslation;
use App\Models\Material;

class MaterialTranslationFactory extends Factory
{
    protected $model = MaterialTranslation::class;

    public function definition()
    {
        return [
            'material_id' => Material::factory(),
            'locale' => 'en',
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph(2),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forLocale($locale)
    {
        return $this->state(function (array $attributes) use ($locale) {
            return [
                'locale' => $locale,
            ];
        });
    }
}
