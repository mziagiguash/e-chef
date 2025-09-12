<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class WatchCourseController extends Controller
{
    public function watchCourse($locale, $id)
    {
        try {
            $course = Course::with([
                // Загружаем инструктора с его переводом
                'instructor.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                // Загружаем категорию курса с ее переводом
                'courseCategory.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                // Загружаем уроки с их переводами и материалами
                'lessons' => function($query) {
                    $query->orderBy('id');
                },
                'lessons.quiz',
                'lessons.materials',
                'lessons.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                // Загружаем переводы самого курса
                'translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])->findOrFail($id);

            // Проверка статуса курса
            if ($course->status != 2) {
                abort(404, 'Course is not active');
            }

            // Получаем перевод курса для текущей локали
            $courseTranslation = $course->translations->first();
            if (!$courseTranslation) {
                $courseTranslation = $course->translations()->where('locale', 'en')->first();
            }

            // Получаем перевод инструктора
            $instructorTranslation = $course->instructor->translations->first();
            if (!$instructorTranslation && $course->instructor) {
                $instructorTranslation = $course->instructor->translations()->where('locale', 'en')->first();
            }

            // Получаем перевод категории курса
            $categoryTranslation = $course->courseCategory->translations->first();
            if (!$categoryTranslation && $course->courseCategory) {
                $categoryTranslation = $course->courseCategory->translations()->where('locale', 'en')->first();
            }

            // Используем методы моделей для получения переведенных данных
            $currentTitle = $courseTranslation->title ?? 'No Title';
            $currentDescription = $courseTranslation->description ?? 'No Description';
            $currentPrerequisites = $courseTranslation->prerequisites ?? 'No Prerequisites';
            $currentKeywords = $courseTranslation->keywords ?? '';

            // Получаем переведенное имя инструктора
            $instructorName = $course->instructor->translated_name ?? 'No Instructor';
            $instructorBio = $course->instructor->translated_bio ?? '';
            $instructorDesignation = $course->instructor->translated_designation ?? '';

            // Получаем переведенное название категории
            $categoryName = $course->courseCategory->translated_category_name ?? 'No Category';

            $progress = 0;
            $completedLessons = 0;
            $totalLessons = $course->lessons->count();

            return view('frontend.watch-course', compact(
                'course',
                'progress',
                'completedLessons',
                'totalLessons',
                'currentTitle',
                'currentDescription',
                'currentPrerequisites',
                'currentKeywords',
                'instructorName',
                'instructorBio',
                'instructorDesignation',
                'categoryName',
                'locale'
            ));

        } catch (\Exception $e) {
            abort(404, 'Course not found: ' . $e->getMessage());
        }
    }
}
