<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonTranslation;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizTranslation;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();

        $lessons = Lesson::with([
            'course.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'quiz.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }
        ])->paginate(10);

        return view('backend.course.lesson.index', compact('lessons'));
    }

    public function create()
    {
        $currentLocale = app()->getLocale();
        $courses = Course::with(['translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        }])->get();

        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

        return view('backend.course.lesson.create', compact('courses', 'locales', 'currentLocale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_ka' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description_en' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_ka' => 'nullable|string',
            'notes_en' => 'nullable|string',
            'notes_ru' => 'nullable|string',
            'notes_ka' => 'nullable|string',
        ]);

        try {
            // Создаем урок
            $lesson = Lesson::create([
                'course_id' => $request->course_id,
            ]);

            // Создаем переводы для всех языков
            $locales = ['en', 'ru', 'ka'];
            foreach ($locales as $locale) {
                LessonTranslation::create([
                    'lesson_id' => $lesson->id,
                    'locale' => $locale,
                    'title' => $request->input("title_{$locale}"),
                    'description' => $request->input("description_{$locale}"),
                    'notes' => $request->input("notes_{$locale}"),
                ]);
            }

            // Автоматически создаем квиз для урока
            $quiz = Quiz::create([
                'lesson_id' => $lesson->id,
                'is_active' => true,
                'passing_score' => 70,
                'max_attempts' => 3,
            ]);

            // Создаем переводы для квиза
            foreach ($locales as $locale) {
                QuizTranslation::create([
                    'quiz_id' => $quiz->id,
                    'locale' => $locale,
                    'title' => "Quiz for " . $request->input("title_{$locale}"),
                    'description' => "Test your knowledge about " . $request->input("title_{$locale}"),
                ]);
            }

            $this->notice::success('Lesson and Quiz Created Successfully');
            return redirect()->route('lesson.index');

        } catch (\Exception $e) {
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $currentLocale = app()->getLocale();
        $lesson = Lesson::with(['translations', 'quiz.translations'])->findOrFail(encryptor('decrypt', $id));

        $courses = Course::with(['translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        }])->get();

        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

        return view('backend.course.lesson.edit', compact('lesson', 'courses', 'locales', 'currentLocale'));
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_ka' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description_en' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_ka' => 'nullable|string',
            'notes_en' => 'nullable|string',
            'notes_ru' => 'nullable|string',
            'notes_ka' => 'nullable|string',
        ]);

        try {
            // Обновляем основную информацию
            $lesson->update([
                'course_id' => $request->course_id,
            ]);

            // Обновляем переводы
            $locales = ['en', 'ru', 'ka'];
            foreach ($locales as $locale) {
                LessonTranslation::updateOrCreate(
                    [
                        'lesson_id' => $lesson->id,
                        'locale' => $locale
                    ],
                    [
                        'title' => $request->input("title_{$locale}"),
                        'description' => $request->input("description_{$locale}"),
                        'notes' => $request->input("notes_{$locale}"),
                    ]
                );
            }

            $this->notice::success('Lesson Updated Successfully');
            return redirect()->route('lesson.index');

        } catch (\Exception $e) {
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        // Каскадное удаление (переводы удалятся автоматически из-за onDelete('cascade'))
        $lesson->delete();

        $this->notice::success('Lesson Deleted Successfully');
        return redirect()->back();
    }
}
