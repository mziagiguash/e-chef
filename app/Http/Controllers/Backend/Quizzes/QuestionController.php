<?php

namespace App\Http\Controllers\Backend\Quizzes;

use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;

use Exception;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index(Request $request)
{
    $locale = $request->get('lang', app()->getLocale());
    $currentLocale = $locale;

    $locales = [
        'en' => 'English',
        'ru' => 'Русский',
        'ka' => 'ქართული'
    ];

    // Получаем quiz_id из запроса
    $quizId = $request->get('quiz_id');
    $quiz = null;

    $questions = Question::with([
        'quiz.translations', // Загружаем квиз и его переводы
        'options.translations', // Загружаем опции и их переводы
        'translations'
    ])
    ->withCount(['options', 'correctOptions'])
    ->when($quizId, function($query) use ($quizId) {
        return $query->where('quiz_id', $quizId);
    })
    ->paginate(10);

    // Если передан quiz_id, загружаем квиз
    if ($quizId) {
        $quiz = Quiz::with('translations')->find($quizId);
    }

    return view('backend.quiz.question.index', compact(
        'questions',
        'quiz', // ← Добавляем переменную quiz
        'quizId', // ← Добавляем quizId
        'locale',
        'currentLocale',
        'locales'
    ));
}
    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $quizzes = Quiz::with(['translations'])->get(); // ← Загружаем все переводы

    $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];
    $currentLocale = request('lang', app()->getLocale());

    return view('backend.quiz.question.create', compact('quizzes', 'locales', 'currentLocale'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|in:single,multiple,text,rating',
            'points' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
            'content.en' => 'required|string',
            'content.ru' => 'nullable|string',
            'content.ka' => 'nullable|string',
            'options.*.a' => 'nullable|string',
            'options.*.b' => 'nullable|string',
            'options.*.c' => 'nullable|string',
            'options.*.d' => 'nullable|string',
        ]);

        // Создаем вопрос
        $question = Question::create([
            'quiz_id' => $request->quiz_id,
            'type' => $request->type,
            'points' => $request->points ?? 1,
            'order' => $request->order ?? 0,
            'is_required' => $request->is_required ?? true,
        ]);

        // Сохраняем переводы вопроса
        foreach (['en', 'ru', 'ka'] as $locale) {
            if (!empty($request->input("content.$locale"))) {
                $question->translations()->create([
                    'locale' => $locale,
                    'content' => $request->input("content.$locale"),
                ]);
            }
        }

        // Для вопросов с опциями создаем опции
        if (in_array($request->type, ['single', 'multiple'])) {
            $optionKeys = ['a', 'b', 'c', 'd'];
            $correctOptions = $request->type === 'single'
                ? [$request->correct_option]
                : ($request->correct_options ?? []);

            foreach ($optionKeys as $key) {
                $optionText = $request->input("options.{$request->current_locale}.{$key}");

                if (!empty($optionText)) {
                    $option = $question->options()->create([
                        'key' => $key,
                        'is_correct' => in_array($key, $correctOptions),
                        'order' => array_search($key, $optionKeys),
                    ]);

                    // Сохраняем переводы опций для всех языков
                    foreach (['en', 'ru', 'ka'] as $locale) {
                        $optionText = $request->input("options.{$locale}.{$key}");
                        if (!empty($optionText)) {
                            $option->translations()->create([
                                'locale' => $locale,
                                'text' => $optionText,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('question.index', ['lang' => $request->input('current_locale', app()->getLocale())])
            ->with('success', 'Question Saved');

    } catch (Exception $e) {
        \Log::error('Question store error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
{
    $locale = $request->get('lang', app()->getLocale());
    $currentLocale = $locale;

    $locales = [
        'en' => 'English',
        'ru' => 'Русский',
        'ka' => 'ქართული'
    ];

    // Загружаем вопрос с переводами и опциями
    $question = Question::with(['translations', 'options', 'quiz'])
        ->findOrFail($id);

    $quizzes = Quiz::all(); // Получаем все квизы для выпадающего списка

    return view('backend.quiz.question.edit', compact(
        'question',
        'quizzes',
        'locale',
        'currentLocale',
        'locales'
    ));
}
 /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    try {
        // Находим вопрос
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|in:single,multiple,text,rating',
            'points' => 'integer|min:1',
            'order' => 'integer|min:0',
            'is_required' => 'boolean',
            'max_choices' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.en.content' => 'required|string',
            'translations.ru.content' => 'nullable|string',
            'translations.ka.content' => 'nullable|string'
        ]);

        // Обновляем основные поля
        $question->update([
            'quiz_id' => $validated['quiz_id'],
            'type' => $validated['type'],
            'points' => $validated['points'] ?? $question->points,
            'order' => $validated['order'] ?? $question->order,
            'is_required' => $validated['is_required'] ?? $question->is_required,
            'max_choices' => $validated['max_choices'] ?? $question->max_choices,
        ]);

        // Обновляем переводы
        foreach (['en', 'ru', 'ka'] as $locale) {
            if (isset($validated['translations'][$locale]['content'])) {
                $question->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['content' => $validated['translations'][$locale]['content']]
                );
            }
        }

        return redirect()->route('question.index', [
            'lang' => $request->get('lang', 'en')
        ])->with('success', 'Question updated successfully');

    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Error updating question: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $question = Question::findOrFail(encryptor('decrypt', $id));

            // Удаляем связанные переводы
            $question->translations()->delete();

            if ($question->delete()) {
                $this->notice::success('Question Deleted!');
                return redirect()->back();
            }
        } catch (Exception $e) {
            \Log::error('Question delete error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
