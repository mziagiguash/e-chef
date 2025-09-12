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

    $questions = Question::with([
        'quiz',
        'options',
        'correctOptions',
        'translations' => function($query) use ($locale) {
            $query->where('locale', $locale);
        }
    ])
    ->withCount(['options', 'correctOptions'])
    ->paginate(10);


    return view('backend.quiz.question.index', compact(
        'questions',
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
    $quizzes = Quiz::with(['translations' => function($q) {
        $q->where('locale', app()->getLocale());
    }])->get();

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
            'quizId' => 'required|exists:quizzes,id',
            'questionType' => 'required|in:multiple_choice,true_false,short_answer',
            'correctAnswer' => 'nullable|in:a,b,c,d',
        ]);

        $question = new Question;
        $question->quiz_id = $request->quizId;
        $question->type = $request->questionType;
        $question->content = null; // Основное содержание будет в переводах
        $question->correct_answer = $request->correctAnswer;

        if ($question->save()) {
            // Сохраняем переводы для всех языков
            $locales = ['en', 'ru', 'ka'];

            foreach ($locales as $locale) {
                $translation = new QuestionTranslation([
                    'locale' => $locale,
                    'content' => $request->input("questionContent_$locale", ''),
                    'option_a' => $request->input("optionA_$locale", ''),
                    'option_b' => $request->input("optionB_$locale", ''),
                    'option_c' => $request->input("optionC_$locale", ''),
                    'option_d' => $request->input("optionD_$locale", ''),
                ]);

                $question->translations()->save($translation);
            }

            $this->notice::success('Question Saved');
            return redirect()->route('question.index', ['lang' => $request->input('current_locale', app()->getLocale())]);
        }

        $this->notice::error('Please try again');
        return redirect()->back()->withInput();

    } catch (Exception $e) {
        \Log::error('Question store error: ' . $e->getMessage());
        $this->notice::error('Error: ' . $e->getMessage());
        return redirect()->back()->withInput();
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
