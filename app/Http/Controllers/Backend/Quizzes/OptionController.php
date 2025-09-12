<?php

namespace App\Http\Controllers\Backend\Quizzes;

use App\Models\Option;
use App\Http\Controllers\Controller;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;
use App\Models\Question;
use Exception;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $locale = $request->get('lang', app()->getLocale());
    $currentLocale = $locale;

    // Используем конфиг из app.php
    $availableLocales = config('app.available_locales');
    $locales = [];
    foreach ($availableLocales as $code => $data) {
        $locales[$code] = $data[1]; // Берем только название языка
    }

    // Query для options с переводом
    $query = Option::with([
        'question.translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        },
        'translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }
    ]);

    // Фильтр по question_id если есть
    if ($request->has('question_id')) {
        $query->where('question_id', $request->question_id);
    }

    $options = $query->paginate(10);

    return view('backend.quiz.option.index', compact(
        'options',
        'locale',
        'currentLocale',
        'locales'
    ));
}

    /**
     * Show the form for creating a new resource.
     */
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'is_correct' => 'boolean',
            'order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.en.option_text' => 'required|string',
            'translations.ru.option_text' => 'nullable|string',
            'translations.ka.option_text' => 'nullable|string'
        ]);

        // Создаем опцию
        $option = Option::create([
            'question_id' => $validated['question_id'],
            'option_text' => $validated['translations']['en']['option_text'], // Основное значение из английского
            'is_correct' => $validated['is_correct'] ?? false,
            'order' => $validated['order'] ?? 0
        ]);

        // Сохраняем переводы
        foreach (['en', 'ru', 'ka'] as $locale) {
            if (isset($validated['translations'][$locale]['option_text']) &&
                !empty(trim($validated['translations'][$locale]['option_text']))) {
                $option->translations()->create([
                    'locale' => $locale,
                    'option_text' => $validated['translations'][$locale]['option_text']
                ]);
            }
        }

        return redirect()->route('option.index', [
            'question_id' => $validated['question_id'],
            'lang' => $request->get('lang', 'en')
        ])->with('success', 'Option created successfully');

    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Error creating option: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Store a newly created resource in storage.
     */
public function create(Request $request)
{
    $locale = $request->get('lang', app()->getLocale());
    $currentLocale = $locale;

    $locales = [
        'en' => 'English',
        'ru' => 'Русский',
        'ka' => 'ქართული'
    ];

    $questions = Question::with(['quiz', 'translations'])->get();

    // Если нет вопросов - показываем ошибку
    if ($questions->count() === 0) {
        return redirect()->route('question.index')
            ->with('error', 'Please create a question first before adding options');
    }

    // Автоматически выбираем первый вопрос, если question_id не передан
    $questionId = $request->get('question_id', $questions->first()->id);

    return view('backend.quiz.option.create', compact(
        'locale',
        'currentLocale',
        'locales',
        'questions',
        'questionId'
    ));
}
    /**
     * Show the form for editing the specified resource.
     */

        public function show($id)
    {
        // ... существующий код show ...
    }
    public function edit($id)
{
    $locale = request()->get('lang', app()->getLocale());
    $currentLocale = $locale;

    $locales = [
        'en' => 'English',
        'ru' => 'Русский',
        'ka' => 'ქართული'
    ];

    $option = Option::with('translations')->findOrFail($id);
    $questions = Question::with(['quiz', 'translations'])->get(); // Добавляем получение вопросов

    return view('backend.quiz.option.edit', compact(
        'option',
        'questions', // Добавляем questions в compact
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
        $option = Option::findOrFail($id);

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id', // Добавляем валидацию question_id
            'translations' => 'required|array',
            'translations.*.option_text' => 'required|string',
            'is_correct' => 'boolean',
            'order' => 'integer|min:0'
        ]);

        // Обновляем основное значение
        $option->update([
            'question_id' => $validated['question_id'],
            'option_text' => $validated['translations']['en']['option_text'] ?? $option->option_text,
            'is_correct' => $validated['is_correct'] ?? $option->is_correct,
            'order' => $validated['order'] ?? $option->order
        ]);

        // Обновляем переводы
        foreach (['en', 'ru', 'ka'] as $lang) {
            if (isset($validated['translations'][$lang])) {
                $option->translations()->updateOrCreate(
                    ['locale' => $lang],
                    ['option_text' => $validated['translations'][$lang]['option_text']]
                );
            }
        }

        return redirect()->route('option.index', [
            'question_id' => $option->question_id, // Используем question_id из обновленной опции
            'lang' => $request->get('lang', 'en')
        ])->with('success', 'Option updated successfully');

    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Error updating option: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $option = Option::findOrFail($id);
            $questionId = $option->question_id;
            $option->delete();

            return redirect()->route('option.index', ['question_id' => $questionId])
                ->with('success', 'Option deleted successfully');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting option: ' . $e->getMessage());
        }
    }

    /**
     * Toggle option correctness
     */
    public function toggleCorrectness($id)
    {
        try {
            $option = Option::findOrFail($id);
            $option->update(['is_correct' => !$option->is_correct]);

            return redirect()->back()
                ->with('success', 'Option correctness updated');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating option: ' . $e->getMessage());
        }
    }
}
