<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Helpers\CurrencyHelper;

class CourseDetailController extends Controller
{
    public function show($locale, $id)
    {
        try {
            app()->setLocale($locale);

            $course = Course::with([
                'instructor.translations',
                'courseCategory.translations',
                'lessons' => function($query) {
                    $query->where('status', 1)->orderBy('order');
                },
                'translations'
            ])->where('status', 2)->findOrFail($id);

            // Получаем переводы
            $courseTranslation = $course->translations->where('locale', $locale)->first();
            $currentTitle = $courseTranslation->title ?? $course->title;
            $currentDescription = $courseTranslation->description ?? $course->description;
            $currentPrerequisites = $courseTranslation->prerequisites ?? $course->prerequisites;

            // Исправлено: добавлены значения по умолчанию
            $instructorName = 'Unknown Instructor';
            $instructorBio = 'No biography available';

            if ($course->instructor) {
                $instructorTranslation = $course->instructor->translations->where('locale', $locale)->first();
                $instructorName = $instructorTranslation->name ?? $course->instructor->name ?? 'Unknown Instructor';
                $instructorBio = $instructorTranslation->bio ?? $course->instructor->bio ?? 'No biography available';
            }

            $currencySymbol = CurrencyHelper::getSymbol();
            $currencyRate = CurrencyHelper::getRate(); // ← ЭТОЙ ПЕРЕМЕННОЙ НЕ БЫЛО!

            return view('frontend.courses.courseDetails', compact( // ← ИСПРАВЬТЕ ПУТЬ!
                'course',
                'currentTitle',
                'currentDescription',
                'currentPrerequisites',
                'instructorName',
                'instructorBio',
                'currencySymbol',
                'currencyRate',
                'locale'
            ));

        } catch (\Exception $e) {
            abort(404, 'Course not found: ' . $e->getMessage());
        }
    }
}
