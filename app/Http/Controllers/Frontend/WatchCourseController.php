<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Http\Helpers\CurrencyHelper;

class WatchCourseController extends Controller
{

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
    public function show($locale, Course $course)
{
    try {
        // Установка языка
        $this->setLocale($locale);

        // Проверяем статус курса
        if ($course->status != 2) {
            abort(404, 'Course is not active');
        }

        // Получаем студента
        $student = $this->getCurrentStudent();
        $hasAccess = $this->checkCourseAccess($course, $student);

        \Log::debug('Course access check', [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'course_type' => $course->course_type,
            'course_price' => $course->price,
            'student_id' => $student ? $student->id : null,
            'has_access' => $hasAccess,
            'student_session' => session('student_id')
        ]);

        if ($hasAccess) {
            // Показываем полную версию
            return $this->showFullCourse($locale, $course, $student);
        } else {
            // Показываем упрощенную версию
            return $this->showSimpleCourse($locale, $course, $student);
        }

    } catch (\Exception $e) {
        \Log::error('Course show error: ' . $e->getMessage());
        abort(404, 'Course not found');
    }
}

    /**
     * Get current student from session
     */
// В WatchCourseController добавляем в метод getCurrentStudent
private function getCurrentStudent()
{
    $studentId = session('student_id');

    // 🔴 ДОБАВЛЕНО: Если нет student_id в сессии, пробуем получить из userId
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
     * Check if student has access to the course
     */
    private function checkCourseAccess(Course $course, $student = null)
    {
        if (!$student) {
            $student = $this->getCurrentStudent();
        }

        if (!$student) {
            return false;
        }

        \Log::debug('Checking course access for student', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'course_type' => $course->course_type,
            'course_price' => $course->price
        ]);

        // 1. Проверяем через Enrollment (платные курсы)
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->where('payment_status', Enrollment::PAYMENT_COMPLETED)
            ->first();

        if ($enrollment) {
            \Log::debug('Access granted via enrollment', ['enrollment_id' => $enrollment->id]);
            return true;
        }

        // 2. Проверяем через purchasedCourses (бесплатные курсы)
        if ($student->hasPurchasedCourse($course->id)) {
            \Log::debug('Access granted via purchased courses');
            return true;
        }

        // 3. Для бесплатных курсов автоматически зачисляем студента
        if ($course->course_type === 'free' || $course->price == 0) {
            \Log::info('Auto-enrolling student in free course', [
                'student_id' => $student->id,
                'course_id' => $course->id
            ]);

            if ($student->enrollInFreeCourse($course)) {
                return true;
            }
        }

        \Log::debug('No access found for student');
        return false;
    }

    /**
     * Display course for enrolled students (always full version)
     */
    public function showEnrolledCourse($locale, Course $course)
    {
        try {
            $student = $this->getCurrentStudent();

            // Проверяем, что студент действительно зачислен
            if (!$student) {
                return redirect()->route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]);
            }

            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->where('payment_status', Enrollment::PAYMENT_COMPLETED)
                ->first();

            if (!$enrollment) {
                return redirect()->route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]);
            }

            // Всегда показываем полную версию
            return $this->showFullCourse($locale, $course, $student);

        } catch (\Exception $e) {
            \Log::error('Enrolled course show error: ' . $e->getMessage());
            return redirect()->route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]);
        }
    }
/**
 * Show full course for students who have access
 */
private function showFullCourse($locale, Course $course, $student = null)
{
    try {
        // Установка языка
        $this->setLocale($locale);

        // Проверяем статус курса
        if ($course->status != 2) {
            abort(404, 'Course is not active');
        }

        // Если студент не передан, получаем текущего
        if (!$student) {
            $student = $this->getCurrentStudent();
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

        // 🔴 ИСПРАВЛЕНО: Получаем прогресс по каждому уроку из student_lesson_progress
        $userLessonProgress = [];
        $progress = 0;
        $completedLessons = 0;
        $totalLessons = $course->lessons->count();
        $canGenerateCertificate = false;
        $studentId = null;

        if ($student) {
            $studentId = $student->id;

            // Получаем прогресс для каждого урока
            foreach ($course->lessons as $index => $lesson) {
                $lessonProgress = $this->getLessonProgress($student->id, $lesson->id);

                // Определяем доступность урока
                $isFirstLesson = $index === 0;
                $previousLesson = $index > 0 ? $course->lessons[$index - 1] : null;

                // Урок доступен если он первый ИЛИ предыдущий урок завершен
                $isAvailable = $isFirstLesson ||
                              ($previousLesson &&
                               ($userLessonProgress[$previousLesson->id]['is_completed'] ?? false));

                $userLessonProgress[$lesson->id] = [
                    'progress' => $lessonProgress['progress'],
                    'is_completed' => $lessonProgress['is_completed'],
                    'is_available' => $isAvailable
                ];

                if ($lessonProgress['is_completed']) {
                    $completedLessons++;
                }
            }

            $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
            $canGenerateCertificate = $progress >= 100;
        }

        // Используем хелпер для получения символа валюты
        $currencySymbol = CurrencyHelper::getSymbol();

        \Log::info('Showing full course version', [
            'course_id' => $course->id,
            'student_id' => $studentId,
            'progress' => $progress,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'lesson_progress' => $userLessonProgress
        ]);

        $hasAccess = true;

        return view('frontend.courses.course-single', compact(
            'course',
            'student',
            'hasAccess',
            'progress',
            'completedLessons',
            'totalLessons',
            'canGenerateCertificate',
            'studentId',
            'currentTitle',
            'currentDescription',
            'currentPrerequisites',
            'currentKeywords',
            'instructorName',
            'instructorBio',
            'instructorDesignation',
            'categoryName',
            'locale',
            'currencySymbol',
            'userLessonProgress' // 🔴 ПЕРЕДАЕМ прогресс по урокам в шаблон
        ));

    } catch (\Exception $e) {
        \Log::error('Full course show error: ' . $e->getMessage());
        abort(404, 'Course not found: ' . $e->getMessage());
    }
}
/**
 * 🔴 ИСПРАВЛЕНО: Метод для получения прогресса урока из student_lesson_progress
 */
private function getLessonProgress($studentId, $lessonId)
{
    $progressRecord = \DB::table('student_lesson_progress')
        ->where('student_id', $studentId)
        ->where('lesson_id', $lessonId)
        ->first();

    \Log::debug('Getting lesson progress', [
        'student_id' => $studentId,
        'lesson_id' => $lessonId,
        'progress_record' => $progressRecord
    ]);

    if ($progressRecord) {
        return [
            'progress' => $progressRecord->progress ?? 0,
            'is_completed' => (bool)($progressRecord->is_completed ?? false)
        ];
    }

    return [
        'progress' => 0,
        'is_completed' => false
    ];
}

/**
 * Show simple course preview for students who don't have access
 */
private function showSimpleCourse($locale, Course $course, $student = null)
{
    try {
        // Установка языка
        $this->setLocale($locale);

        // Проверяем статус курса
        if ($course->status != 2) {
            abort(404, 'Course is not active');
        }

        // Если студент не передан, получаем текущего
        if (!$student) {
            $student = $this->getCurrentStudent();
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

        // Устанавливаем значения по умолчанию для переменных прогресса
        $hasAccess = false;
        $progress = 0;
        $completedLessons = 0;
        $canGenerateCertificate = false;
        $studentId = $student ? $student->id : null;

        \Log::info('Showing simple course version', [
            'course_id' => $course->id,
            'student_id' => $studentId,
            'reason' => 'No access'
        ]);

        return view('frontend.courses.course-single', compact(
            'course',
            'student',
            'hasAccess',
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
            'totalLessons',
            'progress',
            'completedLessons',
            'canGenerateCertificate',
            'studentId'
        ));

    } catch (\Exception $e) {
        \Log::error('Simple course show error: ' . $e->getMessage());
        abort(404, 'Course not found');
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
