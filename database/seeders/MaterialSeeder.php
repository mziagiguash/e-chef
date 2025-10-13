<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Lesson;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        $lessons = Lesson::all();

        foreach ($lessons as $lesson) {
            // Создаем 2-4 материала для каждого урока
            $materialsCount = rand(2, 4);

            for ($i = 1; $i <= $materialsCount; $i++) {
                $material = Material::create([
                    'lesson_id' => $lesson->id,
                    'title' => $this->generateMaterialTitle($i),
                    'type' => $this->generateMaterialType($i),
                    'content' => $this->generateMaterialContent($i),
                    'content_url' => $this->generateContentUrl($i),
                ]);

                // Создаем переводы для каждого языка
                $locales = ['en', 'ru', 'ka'];

                foreach ($locales as $locale) {
                    $material->translations()->create([
                        'locale' => $locale,
                        'title' => $this->generateTranslatedTitle($locale, $i),
                        'content' => $this->generateTranslatedContent($locale),
                    ]);
                }
            }
        }
    }

    private function generateMaterialTitle(int $index): string
    {
        $titles = [
            "Lecture Notes",
            "Video Tutorial",
            "Practice Exercises",
            "Reference Materials",
            "Code Examples",
            "Additional Resources",
            "Study Guide",
            "Quick Reference"
        ];

        return $titles[array_rand($titles)] . " {$index}";
    }

    private function generateMaterialType(int $index): string
    {
        $types = ['video', 'document', 'audio'];
        return $types[array_rand($types)];
    }

    private function generateMaterialContent(int $index): string
    {
        $contents = [
            "Complete lecture notes with code examples and explanations.",
            "Step-by-step tutorial covering all concepts from the lesson.",
            "Practice exercises to reinforce your understanding.",
            "Additional reference materials and external resources.",
            "Code samples and implementation examples.",
            "Study guide with key points and summaries."
        ];

        return $contents[array_rand($contents)];
    }

    private function generateContentUrl(int $index): string
    {
        $types = ['video', 'document', 'audio'];
        $type = $types[array_rand($types)];
        $uuid = uniqid();

        return match($type) {
            'video' => "https://example.com/videos/material-{$index}-{$uuid}.mp4",
            'document' => "https://example.com/documents/material-{$index}-{$uuid}.pdf",
            'audio' => "https://example.com/audios/material-{$index}-{$uuid}.mp3",
            default => "https://example.com/materials/material-{$index}-{$uuid}"
        };
    }

    private function generateTranslatedTitle(string $locale, int $index): string
    {
        $titles = match($locale) {
            'en' => [
                "Lecture Notes",
                "Video Tutorial",
                "Practice Exercises",
                "Reference Materials",
                "Code Examples",
                "Additional Resources"
            ],
            'ru' => [
                "Лекционные материалы",
                "Видеоурок",
                "Практические упражнения",
                "Справочные материалы",
                "Примеры кода",
                "Дополнительные ресурсы"
            ],
            'ka' => [
                "ლექციის მასალები",
                "ვიდეო გაკვეთილი",
                "პრაქტიკული სავარჯიშოები",
                "საცნობარო მასალები",
                "კოდის მაგალითები",
                "დამატებითი რესურსები"
            ]
        };

        return $titles[array_rand($titles)] . " {$index}";
    }

    private function generateTranslatedContent(string $locale): string
    {
        return match($locale) {
            'en' => "Educational materials to support your learning journey. Includes examples and exercises.",
            'ru' => "Образовательные материалы для поддержки вашего обучения. Включает примеры и упражнения.",
            'ka' => "საგანმანათლებლო მასალები თქვენი სწავლის მხარდასაჭერად. მოიცავს მაგალითებს და სავარჯიშოებს."
        };
    }
}
