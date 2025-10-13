<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
public function show($locale, $course, $lesson)
{
    try {
        \Log::debug('=== LESSON SHOW ===', ['course' => $course, 'lesson' => $lesson]);

        // Если параметры - это ID, находим модели
        if (!is_object($course)) {
            $course = Course::with(['translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }])->findOrFail($course);
        }

        if (!is_object($lesson)) {
            $lesson = Lesson::with([
    'quiz',
    // 🔴 ИСПРАВЛЯЕМ: materials -> lessonMaterials или правильное имя отношения
    'materials' => function($query) use ($locale) {
        $query->with(['translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    },
    'translations' => function($q) use ($locale) {
        $q->where('locale', $locale);
    }
])->findOrFail($lesson);
        }

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем переводы
        $courseTranslation = $course->translations->first() ?? $course->translations()->where('locale', 'en')->first();
        $lessonTranslation = $lesson->translations->first() ?? $lesson->translations()->where('locale', 'en')->first();

        // Проверяем тип видео (YouTube или загруженное)
        $isYouTube = false;
        $youTubeId = null;

        // 🔴 ВАЖНО: Проверяем какое поле используется для видео
        $videoField = $lesson->video_url ?? $lesson->video;
        \Log::debug('Video field check', [
            'video_url' => $lesson->video_url,
            'video' => $lesson->video,
            'used_field' => $videoField
        ]);

        if ($videoField) {
            $isYouTube = $this->isYouTubeUrl($videoField);
            if ($isYouTube) {
                $youTubeId = $this->getYouTubeId($videoField);
            }
        }

        // Получаем все уроки курса для боковой панели
        $courseLessons = $course->lessons()
            ->with(['translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }])
            ->orderBy('order')
            ->get();

        \Log::debug('Course lessons count', ['count' => $courseLessons->count()]);

        // Прогресс студента
        $userLessonProgress = [];
        $completedLessonsCount = 0;
        $currentProgress = 0;

        $student = $this->getCurrentStudent();
        \Log::debug('Student found', ['student' => $student ? $student->id : 'null']);

        if ($student) {
            // Получаем прогресс по всем урокам курса
            $progressRecords = $student->lessonProgress()
                ->whereIn('lesson_id', $courseLessons->pluck('id'))
                ->get()
                ->keyBy('lesson_id');

            // Обновляем текущий урок
            $student->updateLessonProgress($lesson, [
                'progress' => 100,
                'video_position' => 0,
                'video_duration' => 0
            ]);

            // Перезагружаем прогресс после обновления
            $progressRecords = $student->lessonProgress()
                ->whereIn('lesson_id', $courseLessons->pluck('id'))
                ->get()
                ->keyBy('lesson_id');

            foreach ($courseLessons as $courseLesson) {
                $progress = $progressRecords->get($courseLesson->id);
                $progressValue = $progress ? $progress->progress : 0;
                $isCompleted = $progress ? $progress->is_completed : false;

                $isAvailable = $this->isLessonAvailable($courseLesson, $courseLessons, $progressRecords);

                $userLessonProgress[$courseLesson->id] = [
                    'progress' => $progressValue,
                    'is_completed' => $isCompleted,
                    'is_available' => $isAvailable
                ];

                if ($isCompleted) {
                    $completedLessonsCount++;
                }

                if ($courseLesson->id == $lesson->id) {
                    $currentProgress = $progressValue;
                }
            }
        } else {
            // Для неавторизованных студентов
            foreach ($courseLessons as $courseLesson) {
                $userLessonProgress[$courseLesson->id] = [
                    'progress' => 0,
                    'is_completed' => false,
                    'is_available' => $this->isLessonAvailable($courseLesson, $courseLessons, collect())
                ];
            }
        }

        $totalLessons = $courseLessons->count();
        $progressPercentage = $totalLessons > 0 ? round(($completedLessonsCount / $totalLessons) * 100) : 0;

        // Навигация между уроками
        $lessons = $course->lessons()->orderBy('order')->get();
        $currentIndex = $lessons->search(function ($item) use ($lesson) {
            return $item->id === $lesson->id;
        });

        $previousLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        \Log::debug('Rendering view', [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'previous_lesson' => $previousLesson ? $previousLesson->id : 'null',
            'next_lesson' => $nextLesson ? $nextLesson->id : 'null'
        ]);

        return view('frontend.lessons.show', compact(
            'locale',
            'course',
            'lesson',
            'courseTranslation',
            'lessonTranslation',
            'previousLesson',
            'nextLesson',
            'currentProgress',
            'isYouTube',
            'youTubeId',
            'courseLessons',
            'userLessonProgress',
            'completedLessonsCount',
            'totalLessons',
            'progressPercentage'
        ));

    } catch (\Exception $e) {
        \Log::error('Lesson show error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        abort(404, 'Course or lesson not found: ' . $e->getMessage());
    }
}

private function getCurrentStudent()
{
    $studentId = session('student_id');

    // Если нет student_id в сессии, пробуем получить из userId
    if (!$studentId && session('userId')) {
        $studentId = encryptor('decrypt', session('userId'));
        if ($studentId) {
            session(['student_id' => $studentId]);
        }
    }

    if ($studentId) {
        return Student::find($studentId);
    }

    return null;
}
    /**
     * Получает текущего студента
     */

    private function getStudentId()
    {
        // Вариант 1: Если студент привязан к пользователю Laravel
        if (Auth::check()) {
            $user = Auth::user();
            // Предполагаем, что у пользователя есть student_id
            return $user->student ?? Student::find($user->student_id);
        }

        return null; // если студент не найден
    }

    /**
     * Проверяет, доступен ли урок для пользователя
     */
    private function isLessonAvailable($lesson, $allLessons, $progressRecords)
    {
        // Если урок первый - всегда доступен
        if ($lesson->order == 1) {
            return true;
        }

        // Находим предыдущий урок
        $previousLesson = $allLessons->where('order', $lesson->order - 1)->first();

        if (!$previousLesson) {
            return true;
        }

        // Проверяем, завершен ли предыдущий урок
        $previousProgress = $progressRecords->get($previousLesson->id);
        return $previousProgress && $previousProgress->is_completed;
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
        $studentId = $this->getStudentId();

        if (!$studentId) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        $lesson = Lesson::findOrFail($lessonId);

        // Используем метод из модели Student для обновления прогресса
        $student->updateLessonProgress($lesson, [
            'progress' => 100,
            'video_position' => 0,
            'video_duration' => 0
        ]);

        return response()->json([
            'success' => true,
            'progress' => 100
        ]);
    }
}
