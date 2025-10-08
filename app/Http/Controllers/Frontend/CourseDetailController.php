<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Helpers\CurrencyHelper;

class CourseDetailController extends Controller
{
    public function showSimpleCourse($locale, Course $course)
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
                'lessons',
                'translations'
            ]);

            // Получаем переводы
            $courseTranslation = $course->translations->where('locale', $locale)->first();
            $instructorTranslation = $course->instructor ? $course->instructor->translations->where('locale', $locale)->first() : null;
            $categoryTranslation = $course->courseCategory ? $course->courseCategory->translations->where('locale', $locale)->first() : null;

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

            $totalLessons = $course->lessons->count();

            // Используем хелпер для получения символа валюты
            $currencySymbol = CurrencyHelper::getSymbol();
            $currencyRate = config('payment.currency_rate', 1);

            return view('frontend.courses.show-simple', compact(
                'course',
                'locale',
                'currentTitle',
                'currentDescription',
                'currentPrerequisites',
                'currentKeywords',
                'instructorName',
                'instructorBio',
                'instructorDesignation',
                'categoryName',
                'currencySymbol',
                'currencyRate',
                'totalLessons'
            ));

        } catch (\Exception $e) {
            \Log::error('Course show simple error: ' . $e->getMessage());
            abort(404, 'Course not found');
        }
    }

    private function setLocale($locale)
    {
        $lang = $locale ?? request()->get('lang', session('locale', 'en'));
        if (in_array($lang, ['en', 'ru', 'ka'])) {
            app()->setLocale($lang);
            session()->put('locale', $lang);
        }
    }
}
