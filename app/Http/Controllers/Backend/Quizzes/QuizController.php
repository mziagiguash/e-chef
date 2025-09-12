<?php

namespace App\Http\Controllers\Backend\Quizzes;

use App\Models\Quiz;
use App\Models\QuizTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use Exception;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $locale = $request->get('lang', app()->getLocale());

    $quizzes = Quiz::with([
        'translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        },
        'lesson.translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        },
        'lesson.course.translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }
    ])->withCount('questions')
    ->paginate(10);

    $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

    return view('backend.quiz.quizzes.index', compact('quizzes', 'locale', 'locales'));
}

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $currentLocale = request('lang', app()->getLocale());

    // Загружаем уроки с переводами и курсами
    $lessons = Lesson::with([
        'translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        },
        'course.translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        }
    ])->get();

    $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

    return view('backend.quiz.quizzes.create', compact('lessons', 'locales', 'currentLocale'));
}

    public function store(Request $request)
    {
        try {
            $request->validate([
                'lessonId' => 'required|exists:lessons,id',
                'passing_score' => 'required|integer|min:0|max:100',
            ]);

            $quiz = new Quiz;
            $quiz->lesson_id = $request->lessonId;
            $quiz->order = $request->get('order', 0);
            $quiz->is_active = $request->has('is_active');
            $quiz->time_limit = $request->get('time_limit', 0);
            $quiz->passing_score = $request->passing_score;
            $quiz->max_attempts = $request->get('max_attempts', 0);

            if ($quiz->save()) {
                // Сохраняем переводы для всех языков
                $locales = ['en', 'ru', 'ka'];

                foreach ($locales as $locale) {
                    $translation = new QuizTranslation([
                        'locale' => $locale,
                        'title' => $request->input("title_$locale", ''),
                        'description' => $request->input("description_$locale", ''),
                    ]);

                    $quiz->translations()->save($translation);
                }

                $this->notice::success('Quiz Saved');
                return redirect()->route('quiz.index', ['lang' => $request->input('current_locale', app()->getLocale())]);
            }

            $this->notice::error('Please try again');
            return redirect()->back()->withInput();

        } catch (Exception $e) {
            \Log::error('Quiz store error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */


public function edit($id)
{
    $currentLocale = request('lang', app()->getLocale());
    $quiz = Quiz::with('translations')->findOrFail(encryptor('decrypt', $id));

    // Загружаем уроки с переводами и курсами
    $lessons = Lesson::with([
        'translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        },
        'course.translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        }
    ])->get();

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'lessonId' => 'required|exists:lessons,id',
                'passing_score' => 'required|integer|min:0|max:100',
            ]);

            $quiz = Quiz::findOrFail(encryptor('decrypt', $id));
            $quiz->lesson_id = $request->lessonId;
            $quiz->order = $request->get('order', 0);
            $quiz->is_active = $request->has('is_active');
            $quiz->time_limit = $request->get('time_limit', 0);
            $quiz->passing_score = $request->passing_score;
            $quiz->max_attempts = $request->get('max_attempts', 0);

            if ($quiz->save()) {
                // Обновляем переводы
                $locales = ['en', 'ru', 'ka'];

                foreach ($locales as $locale) {
                    $translationData = [
                        'title' => $request->input("title_$locale", ''),
                        'description' => $request->input("description_$locale", ''),
                    ];

                    $quiz->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translationData
                    );
                }

                $this->notice::success('Quiz Updated');
                return redirect()->route('quiz.index', ['lang' => $request->input('current_locale', app()->getLocale())]);
            } else {
                $this->notice::error('Please try again');
                return redirect()->back()->withInput();
            }
        } catch (Exception $e) {
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $quiz = Quiz::findOrFail(encryptor('decrypt', $id));

            // Удаляем связанные переводы
            $quiz->translations()->delete();

            if ($quiz->delete()) {
                $this->notice::success('Quiz Deleted!');
                return redirect()->back();
            }
        } catch (Exception $e) {
            \Log::error('Quiz delete error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
