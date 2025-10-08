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

            // Получаем все уроки курса для боковой панели
            $courseLessons = $course->lessons()
                ->with(['translations' => function($q) use ($locale) {
                    $q->where('locale', $locale);
                }])
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            // ИСПРАВЛЕНО: Работаем напрямую со студентами
            $userLessonProgress = [];
            $completedLessonsCount = 0;
            $currentProgress = 100; // При переходе на урок сразу 100%

            // Получаем ID студента из сессии или другого источника
            $studentId = $this->getStudentId();

            if ($studentId) {
                $student = Student::find($studentId);

                if ($student) {
                    // Получаем прогресс по всем урокам курса
                    $progressRecords = $student->lessonProgress()
                        ->whereIn('lesson_id', $courseLessons->pluck('id'))
                        ->get()
                        ->keyBy('lesson_id');

                    // ОБНОВЛЯЕМ ТЕКУЩИЙ УРОК - отмечаем как завершенный
                    $student->updateLessonProgress($lesson, [
                        'progress' => 100,
                        'video_position' => 0,
                        'video_duration' => 0
                    ]);

                    // Обновляем записи прогресса после сохранения
                    $progressRecords = $student->lessonProgress()
                        ->whereIn('lesson_id', $courseLessons->pluck('id'))
                        ->get()
                        ->keyBy('lesson_id');

                    foreach ($courseLessons as $courseLesson) {
                        $progress = $progressRecords->get($courseLesson->id);
                        $progressValue = $progress ? $progress->progress : 0;
                        $isCompleted = $progress ? $progress->is_completed : false;

                        // Урок доступен если он первый ИЛИ предыдущий завершен
                        $isAvailable = $this->isLessonAvailable($courseLesson, $courseLessons, $progressRecords);

                        $userLessonProgress[$courseLesson->id] = [
                            'progress' => $progressValue,
                            'is_completed' => $isCompleted,
                            'is_available' => $isAvailable
                        ];

                        if ($isCompleted) {
                            $completedLessonsCount++;
                        }

                        // Прогресс текущего урока
                        if ($courseLesson->id == $lesson->id) {
                            $currentProgress = $progressValue;
                        }
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

            // Инструктор
            $instructor = $course->instructor;
            $instructorTranslation = $instructor->translations->where('locale', $locale)->first()
                ?? $instructor->translations->where('locale', 'en')->first();
            $instructorName = $instructorTranslation->name ?? $instructor->name ?? __('No Instructor');

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
                'youTubeId',
                'courseLessons',
                'userLessonProgress',
                'completedLessonsCount',
                'totalLessons',
                'progressPercentage'
            ));

        } catch (\Exception $e) {
            abort(404, 'Course or lesson not found');
        }
    }

    /**
     * Получает ID студента (нужно адаптировать под вашу логику авторизации)
     */
    private function getStudentId()
    {
        // Вариант 1: Если у вас есть сессия с ID студента
        // return session('student_id');

        // Вариант 2: Если студент привязан к пользователю Laravel
        // if (Auth::check()) {
        //     $user = Auth::user();
        //     return $user->student_id; // если есть такое поле
        // }

        // Вариант 3: Для тестирования - возвращаем ID первого студента
        $student = Student::first();
        return $student ? $student->id : null;

        // return null; // если студент не найден
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
