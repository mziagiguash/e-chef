<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Checkout;
use App\Models\StudentLessonProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return redirect()->route('studentLogin', ['locale' => app()->getLocale()])
                           ->with('error', 'Please login as student');
        }

        $student_info = Student::find($student_id);

        if (!$student_info) {
            session()->flush();
            return redirect()->route('studentLogin', ['locale' => app()->getLocale()])
                           ->with('error', 'Student not found');
        }

        // Получаем все enrollment с отношениями
        $all_enrollments = Enrollment::with([
                'course',
                'course.instructor',
                'course.translations',
                'course.lessons'
            ])
            ->where('student_id', $student_id)
            ->where('payment_status', Enrollment::PAYMENT_COMPLETED)
            ->get();

        // 🔴 ИСПРАВЛЕННАЯ ФУНКЦИЯ РАСЧЕТА ПРОГРЕССА
        $calculateProgress = function($enrollment) use ($student_id) {
            $course = $enrollment->course;
            if (!$course) return 0;

            // Получаем общее количество уроков в курсе
            $totalLessons = $course->lessons->count();
            if ($totalLessons === 0) return 0;

            // 🔴 ИСПРАВЛЕНО: Используем модель StudentLessonProgress
            try {
                $completedLessons = StudentLessonProgress::where('student_id', $student_id)
                    ->where('course_id', $course->id)
                    ->where('is_completed', true)
                    ->count();
            } catch (\Exception $e) {
                \Log::error('Error calculating progress: ' . $e->getMessage());
                $completedLessons = 0;
            }

            return $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
        };

        // Рассчитываем прогресс для каждого enrollment
        $all_enrollments->each(function($enrollment) use ($calculateProgress) {
            $enrollment->progress_percentage = $calculateProgress($enrollment);
            $enrollment->is_completed = $enrollment->progress_percentage >= 100;

            \Log::debug('Course progress calculated', [
                'course_id' => $enrollment->course->id,
                'course_title' => $enrollment->course->title,
                'progress' => $enrollment->progress_percentage,
                'is_completed' => $enrollment->is_completed
            ]);
        });

        // Разделяем курсы по статусу
        $active_enrollments = $all_enrollments->filter(function($enrollment) {
            return $enrollment->progress_percentage > 0 && $enrollment->progress_percentage < 100;
        })->values();

        $completed_enrollments = $all_enrollments->filter(function($enrollment) {
            return $enrollment->progress_percentage >= 100;
        })->values();

        // Пагинация для каждой вкладки
        $all_enrollments_paginated = $this->paginateCollection($all_enrollments, 9, 'all_page');
        $active_enrollments_paginated = $this->paginateCollection($active_enrollments, 9, 'active_page');
        $completed_enrollments_paginated = $this->paginateCollection($completed_enrollments, 9, 'completed_page');

        // Счетчики для статистики
        $enrolled_courses_count = $all_enrollments->count();
        $completed_courses_count = $completed_enrollments->count();

        $course = Course::get();

        $checkout = Checkout::where('student_id', $student_id)
                          ->latest()
                          ->get();

        // Для обратной совместимости
        $enrollment = $all_enrollments_paginated;

        // Логи для отладки
        \Log::info('Dashboard statistics', [
            'student_id' => $student_id,
            'total_enrollments' => $all_enrollments->count(),
            'active_enrollments' => $active_enrollments->count(),
            'completed_enrollments' => $completed_enrollments->count(),
            'enrolled_courses_count' => $enrolled_courses_count,
            'completed_courses_count' => $completed_courses_count
        ]);

        return view('students.dashboard', compact(
            'student_info',
            'enrollment',
            'all_enrollments',
            'active_enrollments',
            'completed_enrollments',
            'all_enrollments_paginated',
            'active_enrollments_paginated',
            'completed_enrollments_paginated',
            'enrolled_courses_count',
            'completed_courses_count',
            'course',
            'checkout'
        ));
    }

    /**
     * Пагинация для коллекций
     */
    private function paginateCollection($collection, $perPage = 9, $pageName = 'page')
    {
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }
    public function updateProgress(Request $request, Lesson $lesson)
{
    $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

    if (!$student_id) {
        return response()->json(['error' => 'Student not found'], 401);
    }

    $student = Student::find($student_id);

    $progress = $request->input('progress', 0);
    $video_position = $request->input('video_position', 0);
    $video_duration = $request->input('video_duration', 0);

    // Обновляем прогресс урока
    $lessonProgress = $student->updateLessonProgress($lesson, [
        'progress' => $progress,
        'video_position' => $video_position,
        'video_duration' => $video_duration
    ]);

    // Получаем общий прогресс по курсу
    $courseProgress = $student->getCourseProgress($lesson->course);

    return response()->json([
        'lesson_progress' => $progress,
        'course_progress' => $courseProgress,
        'is_completed' => $lessonProgress->is_completed
    ]);
}
public function debugProgress($courseId)
{
    $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

    if (!$student_id) {
        return "Student not logged in";
    }

    $course = Course::with('lessons')->find($courseId);
    $totalLessons = $course->lessons->count();

    $completedLessons = StudentLessonProgress::where('student_id', $student_id)
        ->where('course_id', $courseId)
        ->where('is_completed', true)
        ->count();

    $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

    return [
        'course_id' => $courseId,
        'total_lessons' => $totalLessons,
        'completed_lessons' => $completedLessons,
        'progress' => $progress . '%',
        'completed_lesson_ids' => StudentLessonProgress::where('student_id', $student_id)
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->pluck('lesson_id')
    ];
}
}
