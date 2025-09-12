<?php

namespace App\Http\Controllers\Backend\Quizzes;

use App\Models\Answer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Question;
use App\Models\Option;
use Exception;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locale = $request->get('lang', app()->getLocale());

        $answers = Answer::with([
            'attempt.user', // Загружаем attempt и связанного user
            'question.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'option.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }
        ])->paginate(10);

        return view('backend.quiz.answer.index', compact('answers', 'locale'));
    }

    /**
     * Display the specified resource.
     */
public function show(Request $request, string $id)
{
    $locale = $request->get('lang', app()->getLocale());

    $answer = Answer::with([
        'attempt.user',
        'question.translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        },
        'option.translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }
    ])->findOrFail($id);

    return view('backend.quiz.answer.show', compact('answer', 'locale'));
}

/**
 * Move answer to archive
 */
public function archive(string $id)
{
    try {
        $answer = Answer::findOrFail($id);

        // Здесь логика архивации (например, пометить is_archived = true)
        $answer->update(['is_archived' => true]);

        session()->flash('success', 'Answer moved to archive successfully!');
        return redirect()->back();

    } catch (Exception $e) {
        \Log::error('Archive answer error: ' . $e->getMessage());
        session()->flash('error', 'Failed to archive answer.');
        return redirect()->back();
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $answer = Answer::findOrFail($id);

            if ($answer->delete()) {
                // Если у вас есть трейт Notice или подобный
                // $this->notice::success('Answer Deleted!');
                session()->flash('success', 'Answer Deleted!');
                return redirect()->back();
            }
        } catch (Exception $e) {
            \Log::error('Answer delete error: ' . $e->getMessage());
            // $this->notice::error('Error: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
