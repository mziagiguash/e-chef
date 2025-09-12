<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\MaterialTranslation;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем существующие уроки
        $lessons = Lesson::all();

        if ($lessons->isEmpty()) {
            $this->command->info('No lessons found. Please seed lessons first.');
            return;
        }

        $totalMaterials = 0;

        foreach ($lessons as $lesson) {
            // Создаем 3-6 материалов для каждого урока
            $materialsPerLesson = rand(3, 6);

            for ($i = 1; $i <= $materialsPerLesson; $i++) {
                $type = $this->getRandomType($i);

                $material = Material::create([
                    'lesson_id' => $lesson->id,
                    'title' => $this->generateTitle($type, $i),
                    'type' => $type,
                    'content' => $this->generateContent($type),
                    'content_url' => $this->generateContentUrl($type),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Создаем переводы для материала
                $this->createMaterialTranslations($material, $type, $i);

                $totalMaterials++;
            }

            $this->command->info("Created {$materialsPerLesson} materials for lesson ID: {$lesson->id}");
        }

        $this->command->info("✅ Total {$totalMaterials} materials with translations seeded successfully!");
        $this->command->info("📊 For {$lessons->count()} lessons");
    }

    private function getRandomType(int $index): string
    {
        $types = ['video', 'document', 'quiz'];

        // Первый материал обычно видео, последний может быть квизом
        if ($index === 1) return 'video';
        if ($index >= 4 && rand(0, 1)) return 'quiz';

        return $types[array_rand($types)];
    }

    private function generateTitle(string $type, int $index): string
    {
        $titles = [
            'video' => [
                'Introduction Video',
                'Lecture Recording',
                'Tutorial Video',
                'Demo Session'
            ],
            'document' => [
                'Study Guide',
                'Reference Material',
                'Exercise Sheet',
                'Reading Assignment'
            ],
            'quiz' => [
                'Knowledge Check',
                'Practice Quiz',
                'Assessment Test'
            ]
        ];

        return $titles[$type][array_rand($titles[$type])] . " {$index}";
    }

    private function generateContent(string $type): string
    {
        return match($type) {
            'video' => 'Video lecture content for this lesson',
            'document' => 'Study material and references for this topic',
            'quiz' => 'Test your understanding of the concepts covered',
            default => 'Learning material'
        };
    }

    private function generateContentUrl(string $type): ?string
    {
        return match($type) {
            'video' => 'https://example.com/videos/' . uniqid() . '.mp4',
            'document' => 'https://example.com/documents/' . uniqid() . '.pdf',
            'quiz' => null,
            default => null
        };
    }

    private function createMaterialTranslations($material, string $type, int $index): void
    {
        $locales = ['en', 'ru', 'ka'];

        foreach ($locales as $locale) {
            MaterialTranslation::create([
                'material_id' => $material->id,
                'locale' => $locale,
                'title' => $this->getTranslatedTitle($type, $index, $locale),
                'content' => $this->getTranslatedContent($type, $locale),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getTranslatedTitle(string $type, int $index, string $locale): string
    {
        $titles = [
            'video' => [
                'en' => ["Introduction Video", "Lecture Recording", "Tutorial Video", "Demo Session"],
                'ru' => ["Вводное видео", "Запись лекции", "Обучающее видео", "Демо-сессия"],
                'ka' => ["შესავალი ვიდეო", "ლექციის ჩანაწერი", "სასწავლო ვიდეო", "დემო სესია"]
            ],
            'document' => [
                'en' => ["Study Guide", "Reference Material", "Exercise Sheet", "Reading Assignment"],
                'ru' => ["Учебное пособие", "Справочный материал", "Лист с упражнениями", "Задание для чтения"],
                'ka' => ["სასწავლო სახელმძღვანელო", "საცნობარო მასალა", "სავარჯიშო ფურცელი", "კითხვის დავალება"]
            ],
            'quiz' => [
                'en' => ["Knowledge Check", "Practice Quiz", "Assessment Test"],
                'ru' => ["Проверка знаний", "Практический тест", "Оценочный тест"],
                'ka' => ["ცოდნის შემოწმება", "პრაქტიკული ტესტი", "შეფასების ტესტი"]
            ]
        ];

        $availableTitles = $titles[$type][$locale] ?? $titles[$type]['en'];
        return $availableTitles[array_rand($availableTitles)] . " {$index}";
    }

    private function getTranslatedContent(string $type, string $locale): string
    {
        $content = [
            'video' => [
                'en' => 'Video lecture content for this lesson',
                'ru' => 'Видео лекция для этого урока',
                'ka' => 'ვიდეო ლექცია ამ გაკვეთილისთვის'
            ],
            'document' => [
                'en' => 'Study material and references for this topic',
                'ru' => 'Учебный материал и ссылки по этой теме',
                'ka' => 'სასწავლო მასალა და ლიტერატურა ამ თემისთვის'
            ],
            'quiz' => [
                'en' => 'Test your understanding of the concepts covered',
                'ru' => 'Проверьте свое понимание рассмотренных концепций',
                'ka' => 'გამოცადეთ თქვენი理解 ნახcoveredებული კონცეფციების'
            ]
        ];

        return $content[$type][$locale] ?? $content[$type]['en'];
    }
}
