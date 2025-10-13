<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Material;
use App\Models\Lesson;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition()
    {
        $types = ['video', 'document', 'audio'];
        $type = $this->faker->randomElement($types);

        return [
            'lesson_id' => Lesson::factory(),
            'title' => $this->faker->sentence(3),
            'type' => $type,
            'content' => $this->generateContent($type),
            'content_url' => $this->generateContentUrl($type),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateContent($type)
    {
        return match($type) {
            'video' => 'Video lecture material',
            'document' => $this->faker->paragraph(3),
            'audio' => 'audio lecture material',
            default => null
        };
    }

    private function generateContentUrl($type)
    {
        return match($type) {
            'video' => 'https://example.com/videos/' . $this->faker->uuid . '.mp4',
            'document' => 'https://example.com/documents/' . $this->faker->uuid . '.pdf',
            'audio' => 'https://example.com/audios/' . $this->faker->uuid . '.mp3',
            default => null
        };
    }
}
