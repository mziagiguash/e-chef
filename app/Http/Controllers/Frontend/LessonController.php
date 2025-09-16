<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show($locale, $courseId, $lessonId)
    {
        try {
            $course = Course::with(['translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }])->findOrFail($courseId);

            $lesson = Lesson::with([
                'quiz',
                'materials',
                'translations' => function($q) use ($locale) {
                    $q->where('locale', $locale);
                }
            ])->findOrFail($lessonId);

            // Проверяем, принадлежит ли урок курсу
            if ($lesson->course_id != $course->id) {
                abort(404, 'Lesson does not belong to this course');
            }

            // Получаем переводы
            $courseTranslation = $course->translations->first() ?? $course->translations()->where('locale', 'en')->first();
            $lessonTranslation = $lesson->translations->first() ?? $lesson->translations()->where('locale', 'en')->first();

            // Проверяем тип видео (YouTube или загруженное)
            $isYouTube = false;
            $youTubeId = null;

            if ($lesson->video_url) {
                $isYouTube = $this->isYouTubeUrl($lesson->video_url);
                if ($isYouTube) {
                    $youTubeId = $this->getYouTubeId($lesson->video_url);
                }
            }

            // Получаем все уроки курса для навигации
            $lessons = $course->lessons()->with(['translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }])->orderBy('id')->get();

            // Находим текущий индекс для навигации
            $currentIndex = $lessons->search(function ($item) use ($lesson) {
                return $item->id === $lesson->id;
            });

            $previousLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
            $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

            // Получаем перевод имени инструктора
            $instructor = $course->instructor;
            $instructorTranslation = $instructor->translations->where('locale', $locale)->first()
                ?? $instructor->translations->where('locale', 'en')->first();

            $instructorName = $instructorTranslation->name ?? $instructor->name ?? __('No Instructor');

            $currentProgress = 0;

            return view('frontend.lessons.show', compact(
                'locale',
                'course',
                'lesson',
                'courseTranslation',
                'lessonTranslation',
                'instructorName',
                'previousLesson',
                'nextLesson',
                'currentProgress',
                'isYouTube',
                'youTubeId'
            ));

        } catch (\Exception $e) {
            abort(404, 'Course or lesson not found');
        }
    }

    /**
     * Проверяет, является ли URL YouTube ссылкой
     */
    private function isYouTubeUrl($url)
    {
        return preg_match('/youtube\.com|youtu\.be/', $url);
    }

    /**
     * Извлекает ID видео из YouTube URL
     */
    private function getYouTubeId($url)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);
        return $matches[1] ?? null;
    }

    public function updateProgress(Request $request, $locale, $lessonId)
    {
        // Логика сохранения прогресса
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'video_position' => 'required|integer',
            'video_duration' => 'required|integer'
        ]);

        // Здесь будет сохранение прогресса в базу
        // $student->updateLessonProgress($lesson, $validated);

        return response()->json([
            'success' => true,
            'progress' => $validated['progress']
        ]);
    }
}
