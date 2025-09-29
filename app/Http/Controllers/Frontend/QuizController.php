<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\QuestionAnswer;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show($locale, Course $course, Lesson $lesson)
{
    try {
        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение (один квиз на урок)
        $quiz = $lesson->quiz;

        if (!$quiz) {
            abort(404, 'Quiz not found for this lesson');
        }

        // Загружаем вопросы с переводами
        $quiz->load([
            'questions' => function($query) use ($locale) {
                $query->orderBy('order', 'asc')
                      ->with([
                          'translations' => function($q) use ($locale) {
                              $q->where('locale', $locale);
                          },
                          'options' => function($q) use ($locale) {
                              $q->orderBy('order', 'asc')
                                ->with(['translations' => function($q2) use ($locale) {
                                    $q2->where('locale', $locale);
                                }]);
                          }
                      ]);
            }
        ]);

        // Количество попыток
        $attemptsCount = $quiz->attempts()
            ->where('user_id', auth()->id())
            ->count();

        $canAttempt = $quiz->max_attempts === 0 || $attemptsCount < $quiz->max_attempts;

        return view('frontend.quizzes.show', compact(
            'quiz', 'course', 'lesson', 'attemptsCount', 'canAttempt', 'locale'
        ));

    } catch (\Exception $e) {
        abort(404, 'Quiz not found');
    }
}

public function start(Request $request, $locale, Course $course, Lesson $lesson)
{
    try {
        $user = Auth::user();

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение
        $quiz = $lesson->quiz;
        if (!$quiz) {
            abort(404, 'Quiz not found for this lesson');
        }

        // Проверяем максимальное количество попыток
        $attemptsCount = $quiz->attempts()
            ->where('user_id', $user->id)
            ->count();

        if ($quiz->max_attempts > 0 && $attemptsCount >= $quiz->max_attempts) {
            return redirect()->back()
                ->with('error', __('You have reached the maximum number of attempts for this quiz.'));
        }

        // Создаем новую попытку
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'total_questions' => $quiz->questions()->count()
        ]);

        // Перенаправляем на страницу прохождения
        return redirect()->route('frontend.quizzes.attempt', [
            'locale' => $locale,
            'course' => $course->id,
            'lesson' => $lesson->id,
            'attempt' => $attempt->id
        ]);

    } catch (\Exception $e) {
        \Log::error('Quiz start error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', __('Error starting quiz: ') . $e->getMessage());
    }
}
public function attempt($locale, Course $course, Lesson $lesson, $attemptId)
{
    try {
        $user = Auth::user();

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение
        $quiz = $lesson->quiz;
        if (!$quiz) {
            abort(404, 'Quiz not found for this lesson');
        }

        // Находим попытку
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Проверяем статус
        if ($attempt->status === 'completed') {
            return redirect()->route('frontend.quizzes.results', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'attempt' => $attempt->id
            ]);
        }

        // Загружаем вопросы с переводами
        $questions = Question::with([
            'translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'options' => function($q) use ($locale) {
                $q->orderBy('order', 'asc')
                  ->with(['translations' => function($q2) use ($locale) {
                      $q2->where('locale', $locale);
                  }]);
            }
        ])->where('quiz_id', $quiz->id)
          ->orderBy('order', 'asc')
          ->get();

        return view('frontend.quizzes.attempt', compact(
            'quiz', 'course', 'lesson', 'attempt', 'questions', 'locale'
        ));

    } catch (\Exception $e) {
        \Log::error('Quiz attempt error: ' . $e->getMessage());
        return redirect()->route('frontend.quizzes.show', [
            'locale' => $locale,
            'course' => $course->id,
            'lesson' => $lesson->id
        ])->with('error', __('Quiz attempt not found'));
    }
}
public function submit(Request $request, $locale, Course $course, Lesson $lesson, $attemptId)
{
    try {
        $user = Auth::user();

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение
        $quiz = $lesson->quiz;
        if (!$quiz) {
            abort(404, 'Quiz not found for this lesson');
        }

        // Находим попытку
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // ... остальной код метода submit() без изменений
        // просто уберите проверку $quiz->lesson_id
    } catch (\Exception $e) {
        \Log::error('Quiz submit error: ' . $e->getMessage());
        return redirect()->route('frontend.quizzes.attempt', [
            'locale' => $locale,
            'course' => $course->id,
            'lesson' => $lesson->id,
            'attempt' => $attemptId
        ])->with('error', __('Error submitting quiz'));
    }
}

 public function results($locale, Course $course, Lesson $lesson, $attemptId)
{
    try {
        $user = Auth::user();

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение
        $quiz = $lesson->quiz;
        if (!$quiz) {
            abort(404, 'Quiz not found for this lesson');
        }

        // Находим попытку и загружаем связь с quiz
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->with('quiz')
            ->firstOrFail();

        // ... остальной код метода results() без изменений
    } catch (\Exception $e) {
        \Log::error('Quiz results error: ' . $e->getMessage());
        return redirect()->route('frontend.quizzes.show', [
            'locale' => $locale,
            'course' => $course->id,
            'lesson' => $lesson->id
        ])->with('error', __('Results not found'));
    }
}

}
