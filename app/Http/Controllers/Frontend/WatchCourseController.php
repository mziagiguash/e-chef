<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Helpers\CurrencyHelper;

class WatchCourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index($locale)
    {
        try {
            // Установка языка
            $this->setLocale($locale);

            $courses = Course::with([
                'instructor.translations',
                'courseCategory.translations',
                'translations'
            ])
            ->where('status', 2) // Only active courses
            ->orderBy('created_at', 'desc')
            ->paginate(12);

            // Prepare translated data for each course
            $courses->each(function($course) use ($locale) {
                $courseTranslation = $course->translations->where('locale', $locale)->first();
                $instructorTranslation = $course->instructor ? $course->instructor->translations->where('locale', $locale)->first() : null;
                $categoryTranslation = $course->courseCategory ? $course->courseCategory->translations->where('locale', $locale)->first() : null;

                $course->currentTitle = $courseTranslation->title ?? $course->translations->first()->title ?? $course->title ?? __('No Title');
                $course->currentDescription = $courseTranslation->description ?? $course->translations->first()->description ?? $course->description ?? __('No Description');
                $course->instructorName = $instructorTranslation->name ?? $course->instructor->translations->first()->name ?? $course->instructor->name ?? __('No Instructor');
                $course->categoryName = $categoryTranslation->category_name ?? $course->courseCategory->translations->first()->name ?? $course->courseCategory->category_name ?? __('No Category');
            });

            // Для совместимости с существующим шаблоном
            $allCourses = $courses;

            // Используем хелпер для получения символа валюты
            $currencySymbol = CurrencyHelper::getSymbol();

            return view('frontend.courses.index', compact('courses', 'allCourses', 'locale', 'currencySymbol'));

        } catch (\Exception $e) {
            abort(500, 'Error loading courses: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified course.
     */
public function show($locale, Course $course) // Используем привязку модели
{
    try {
        // Установка языка
        $this->setLocale($locale);

        // Проверяем статус курса
        if ($course->status != 2) {
            abort(404, 'Course is not active');
        }

        // Загружаем отношения
        $course->load([
            'instructor.translations',
            'courseCategory.translations',
            'lessons' => function($query) {
                $query->orderBy('order');
            },
            'lessons.quiz',
            'lessons.materials',
            'lessons.translations',
            'translations'
        ]);

        // Получаем переводы для текущего языка
        $courseTranslation = $course->translations->where('locale', $locale)->first();
        $instructorTranslation = $course->instructor ? $course->instructor->translations->where('locale', $locale)->first() : null;
        $categoryTranslation = $course->courseCategory ? $course->courseCategory->translations->where('locale', $locale)->first() : null;

        // Используем переведенные данные или fallback
        $currentTitle = $courseTranslation->title ?? $course->translations->first()->title ?? $course->title ?? __('No Title');
        $currentDescription = $courseTranslation->description ?? $course->translations->first()->description ?? $course->description ?? __('No Description');
        $currentPrerequisites = $courseTranslation->prerequisites ?? $course->translations->first()->prerequisites ?? $course->prerequisites ?? __('No Prerequisites');
        $currentKeywords = $courseTranslation->keywords ?? $course->translations->first()->keywords ?? $course->keywords ?? '';

        // Инструктор
        $instructorName = $instructorTranslation->name ?? $course->instructor->translations->first()->name ?? $course->instructor->name ?? __('No Instructor');
        $instructorBio = $instructorTranslation->bio ?? $course->instructor->translations->first()->bio ?? $course->instructor->bio ?? __('No biography available.');
        $instructorDesignation = $instructorTranslation->designation ?? $course->instructor->translations->first()->designation ?? $course->instructor->designation ?? '';

        // Категория
        $categoryName = $categoryTranslation->category_name ?? $course->courseCategory->translations->first()->name ?? $course->courseCategory->category_name ?? __('No Category');

        $progress = 0;
        $completedLessons = 0;
        $totalLessons = $course->lessons->count();

        // Используем хелпер для получения символа валюты
        $currencySymbol = CurrencyHelper::getSymbol();

        return view('frontend.courses.watch-course', compact(
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
            'locale',
            'currencySymbol'
        ));

    } catch (\Exception $e) {
        abort(404, 'Course not found: ' . $e->getMessage());
    }
}

    /**
     * Set the application locale
     */
    private function setLocale($locale)
    {
        $lang = $locale ?? request()->get('lang', session('locale', 'en'));
        if (in_array($lang, ['en', 'ru', 'ka'])) {
            app()->setLocale($lang);
            session()->put('locale', $lang);
        }
    }
}
