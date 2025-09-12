<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonTranslation;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with([
            'course.translations', // Load all course translations
            'translations',        // Load all lesson translations
            'quiz.translations',   // Load all quiz translations
            'materials.translations' // Load all material translations
        ])->paginate(10);

        return view('backend.course.lesson.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::with('translations')->get();
        return view('backend.course.lesson.create', compact('courses'));
    }

    public function edit($id)
    {
        $lesson = Lesson::with('translations')->findOrFail(encryptor('decrypt', $id));
        $courses = Course::with('translations')->get();

        return view('backend.course.lesson.edit', compact('lesson', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'translations.en.title' => 'required|string|max:255',
            'translations.ru.title' => 'required|string|max:255',
            'translations.ka.title' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ru.description' => 'nullable|string',
            'translations.ka.description' => 'nullable|string',
            'translations.en.notes' => 'nullable|string',
            'translations.ru.notes' => 'nullable|string',
            'translations.ka.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Создаем урок
            $lesson = Lesson::create([
                'course_id' => $validated['course_id'],
            ]);

            // Создаем переводы для всех языков
            $locales = ['en', 'ru', 'ka'];
            foreach ($locales as $locale) {
                if (!empty($request->translations[$locale]['title'])) {
                    LessonTranslation::create([
                        'lesson_id' => $lesson->id,
                        'locale' => $locale,
                        'title' => $request->translations[$locale]['title'],
                        'description' => $request->translations[$locale]['description'] ?? null,
                        'notes' => $request->translations[$locale]['notes'] ?? null,
                    ]);
                }
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
                $lessonTitle = $request->translations[$locale]['title'] ?? 'Lesson';
                QuizTranslation::create([
                    'quiz_id' => $quiz->id,
                    'locale' => $locale,
                    'title' => "Quiz for " . $lessonTitle,
                    'description' => "Test your knowledge about " . $lessonTitle,
                ]);
            }

            DB::commit();

            $this->notice::success('Lesson and Quiz Created Successfully');
            return redirect()->route('lesson.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'translations.en.title' => 'required|string|max:255',
            'translations.ru.title' => 'required|string|max:255',
            'translations.ka.title' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ru.description' => 'nullable|string',
            'translations.ka.description' => 'nullable|string',
            'translations.en.notes' => 'nullable|string',
            'translations.ru.notes' => 'nullable|string',
            'translations.ka.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Обновляем основную информацию
            $lesson->update([
                'course_id' => $validated['course_id'],
            ]);

            // Обновляем переводы
            $locales = ['en', 'ru', 'ka'];
            foreach ($locales as $locale) {
                if (!empty($request->translations[$locale]['title'])) {
                    LessonTranslation::updateOrCreate(
                        [
                            'lesson_id' => $lesson->id,
                            'locale' => $locale
                        ],
                        [
                            'title' => $request->translations[$locale]['title'],
                            'description' => $request->translations[$locale]['description'] ?? null,
                            'notes' => $request->translations[$locale]['notes'] ?? null,
                        ]
                    );
                }
            }

            DB::commit();

            $this->notice::success('Lesson Updated Successfully');
            return redirect()->route('lesson.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        try {
            DB::beginTransaction();

            // Удаляем квиз сначала (если есть)
            if ($lesson->quiz) {
                $lesson->quiz->delete();
            }

            // Удаляем урок (переводы удалятся автоматически из-за onDelete('cascade'))
            $lesson->delete();

            DB::commit();

            $this->notice::success('Lesson Deleted Successfully');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
