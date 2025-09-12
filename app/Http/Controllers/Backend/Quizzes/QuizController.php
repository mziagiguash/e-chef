<?php

namespace App\Http\Controllers\Backend\Quizzes;

use App\Models\Quiz;
use App\Models\QuizTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Exception;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
public function index(Request $request)
{
    $locale = $request->get('lang', app()->getLocale());

    $quizzes = Quiz::with([
        'translations', // Load all translations
        'lesson.translations', // Load all lesson translations
        'lesson.course.translations' // Load all course translations
    ])->withCount('questions')
    ->orderBy('id', 'desc')
    ->paginate(10);

    $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

    return view('backend.quiz.quizzes.index', compact('quizzes', 'locale', 'locales'));
}

    public function create()
    {
        $currentLocale = request('lang', app()->getLocale());

        // Загружаем уроки со всеми переводами
        $lessons = Lesson::with(['translations', 'course.translations'])->get();

        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

        return view('backend.quiz.quizzes.create', compact('lessons', 'locales', 'currentLocale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lessonId' => 'required|exists:lessons,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'translations.en.title' => 'required|string|max:255',
            'translations.ru.title' => 'required|string|max:255',
            'translations.ka.title' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ru.description' => 'nullable|string',
            'translations.ka.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $quiz = new Quiz;
            $quiz->lesson_id = $validated['lessonId'];
            $quiz->order = $request->get('order', 0);
            $quiz->is_active = $request->has('is_active');
            $quiz->time_limit = $request->get('time_limit', 0);
            $quiz->passing_score = $validated['passing_score'];
            $quiz->max_attempts = $request->get('max_attempts', 0);

            if ($quiz->save()) {
                // Сохраняем переводы для всех языков
                $locales = ['en', 'ru', 'ka'];

                foreach ($locales as $locale) {
                    if (!empty($request->translations[$locale]['title'])) {
                        QuizTranslation::create([
                            'quiz_id' => $quiz->id,
                            'locale' => $locale,
                            'title' => $request->translations[$locale]['title'],
                            'description' => $request->translations[$locale]['description'] ?? null,
                        ]);
                    }
                }

                DB::commit();

                $this->notice::success('Quiz Saved');
                return redirect()->route('quiz.index', ['lang' => $request->input('current_locale', app()->getLocale())]);
            }

            DB::rollBack();
            $this->notice::error('Please try again');
            return redirect()->back()->withInput();

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Quiz store error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $currentLocale = request('lang', app()->getLocale());
        $quiz = Quiz::with('translations')->findOrFail(encryptor('decrypt', $id));

        // Загружаем уроки со всеми переводами
        $lessons = Lesson::with(['translations', 'course.translations'])->get();

        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

        // Подготавливаем переводы для формы
        $translations = [];
        foreach ($quiz->translations as $translation) {
            $translations[$translation->locale] = $translation;
        }

        // Заполняем недостающие локали пустыми объектами
        foreach ($locales as $localeCode => $localeName) {
            if (!isset($translations[$localeCode])) {
                $translations[$localeCode] = new QuizTranslation(['locale' => $localeCode]);
            }
        }

        return view('backend.quiz.quizzes.edit', compact('quiz', 'lessons', 'locales', 'currentLocale', 'translations'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'lessonId' => 'required|exists:lessons,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'translations.en.title' => 'required|string|max:255',
            'translations.ru.title' => 'required|string|max:255',
            'translations.ka.title' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ru.description' => 'nullable|string',
            'translations.ka.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $quiz = Quiz::findOrFail(encryptor('decrypt', $id));
            $quiz->lesson_id = $validated['lessonId'];
            $quiz->order = $request->get('order', 0);
            $quiz->is_active = $request->has('is_active');
            $quiz->time_limit = $request->get('time_limit', 0);
            $quiz->passing_score = $validated['passing_score'];
            $quiz->max_attempts = $request->get('max_attempts', 0);

            if ($quiz->save()) {
                // Обновляем переводы
                $locales = ['en', 'ru', 'ka'];

                foreach ($locales as $locale) {
                    if (!empty($request->translations[$locale]['title'])) {
                        QuizTranslation::updateOrCreate(
                            [
                                'quiz_id' => $quiz->id,
                                'locale' => $locale
                            ],
                            [
                                'title' => $request->translations[$locale]['title'],
                                'description' => $request->translations[$locale]['description'] ?? null,
                            ]
                        );
                    }
                }

                DB::commit();

                $this->notice::success('Quiz Updated');
                return redirect()->route('quiz.index', ['lang' => $request->input('current_locale', app()->getLocale())]);
            } else {
                DB::rollBack();
                $this->notice::error('Please try again');
                return redirect()->back()->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Quiz update error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $quiz = Quiz::with(['translations', 'questions.translations', 'questions.options.translations'])
                   ->findOrFail(encryptor('decrypt', $id));

        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

        return view('backend.quiz.quizzes.show', compact('quiz', 'locales'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $quiz = Quiz::findOrFail(encryptor('decrypt', $id));

            // Удаляем связанные переводы
            $quiz->translations()->delete();

            if ($quiz->delete()) {
                DB::commit();
                $this->notice::success('Quiz Deleted!');
                return redirect()->back();
            }

            DB::rollBack();
            $this->notice::error('Please try again');
            return redirect()->back();

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Quiz delete error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
