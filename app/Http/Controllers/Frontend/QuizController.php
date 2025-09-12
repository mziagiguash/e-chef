<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
public function show($locale, $courseId, $lessonId, $quizId)
{
    try {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $quiz = Quiz::findOrFail($quizId);

        // Проверяем принадлежность урока курсу и квиза уроку
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        if ($quiz->lesson_id != $lesson->id) {
            abort(404, 'Quiz does not belong to this lesson');
        }

        // Загружаем отношения с переводами
        $quiz->load([
            'translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'questions' => function($query) {
                $query->orderBy('order', 'asc')->with(['options' => function($q) {
                    $q->orderBy('order', 'asc');
                }]);
            },
            'questions.translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'questions.options.translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }
        ]);

        // Получаем количество попыток пользователя через QuizAttempt
        $attemptsCount = $quiz->attempts()
            ->where('user_id', auth()->id())
            ->count();

        $canAttempt = $quiz->max_attempts === 0 || $attemptsCount < $quiz->max_attempts;

        return view('frontend.quizzes.show', compact(
            'quiz', 'course', 'lesson', 'attemptsCount', 'canAttempt', 'locale'
        ));

    } catch (\Exception $e) {
        abort(404, 'Quiz not found: ' . $e->getMessage());
    }
}

public function results($locale, $courseId, $lessonId, $quizId, $attemptId = null)
{
    try {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $quiz = Quiz::findOrFail($quizId);

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id || $quiz->lesson_id != $lesson->id) {
            abort(404, 'Invalid course, lesson or quiz relationship');
        }

        // Если attemptId не передан, берем последнюю попытку
        if (!$attemptId) {
            $attempt = $quiz->attempts()
                ->where('user_id', auth()->id())
                ->latest()
                ->firstOrFail();
        } else {
            $attempt = QuizAttempt::where('id', $attemptId)
                ->where('quiz_id', $quiz->id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        }

        $passed = $attempt->score >= $quiz->passing_score;

        return view('frontend.quizzes.results', compact(
            'quiz',
            'locale',
            'passed',
            'attempt',
            'course',
            'lesson'
        ));

    } catch (\Exception $e) {
        abort(404, 'Results not found: ' . $e->getMessage());
    }
}


public function start(Request $request, $locale, $course, $lesson, $quiz)
{
    try {
        $course = Course::findOrFail($course);
        $lesson = Lesson::findOrFail($lesson);
        $quiz = Quiz::findOrFail($quiz);
        $user = Auth::user();

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        if ($quiz->lesson_id != $lesson->id) {
            abort(404, 'Quiz does not belong to this lesson');
        }

        // Проверяем максимальное количество попыток
        $attemptsCount = $quiz->attempts()
            ->where('user_id', $user->id)
            ->count();

        if ($quiz->max_attempts > 0 && $attemptsCount >= $quiz->max_attempts) {
            return redirect()->back()
                ->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        // Создаем новую попытку
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'total_questions' => $quiz->questions()->count()
        ]);

        return redirect()->route('frontend.quizzes.attempt', [
            'locale' => $locale,
            'course' => $course->id,
            'lesson' => $lesson->id,
            'quiz' => $quiz->id,
            'attempt' => $attempt->id
        ]);

    } catch (\Exception $e) {
        abort(404, 'Error starting quiz: ' . $e->getMessage());
    }
}

public function attempt($locale, $courseId, $lessonId, $quizId, $attemptId)
{
    try {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $quiz = Quiz::findOrFail($quizId);
        $attempt = QuizAttempt::findOrFail($attemptId);

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        if ($quiz->lesson_id != $lesson->id) {
            abort(404, 'Quiz does not belong to this lesson');
        }

        // Проверяем, что попытка принадлежит пользователю
        if ($attempt->user_id != Auth::id()) {
            abort(403, 'This attempt does not belong to you');
        }

        // Проверяем, что попытка еще активна
        if ($attempt->status === QuizAttempt::STATUS_COMPLETED) {
            abort(400, 'This attempt has already been completed');
        }

        // Загружаем вопросы и варианты ответов
        $quiz->load([
            'questions' => function($query) {
                $query->orderBy('order', 'asc')->with(['options' => function($q) {
                    $q->orderBy('order', 'asc');
                }]);
            },
            'questions.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'questions.options.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }
        ]);

        return view('frontend.quizzes.attempt', compact(
            'quiz',
            'locale',
            'attempt',
            'course',
            'lesson'
        ));

    } catch (\Exception $e) {
        abort(404, 'Error loading quiz attempt: ' . $e->getMessage());
    }
}

public function submitAttempt(Request $request, $locale, $courseId, $lessonId, $quizId, $attemptId)
{
    try {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = Auth::user();
        $attempt = QuizAttempt::findOrFail($attemptId);

        // Проверяем принадлежность и права доступа
        if ($lesson->course_id != $course->id ||
            $quiz->lesson_id != $lesson->id ||
            $attempt->quiz_id != $quiz->id ||
            $attempt->user_id != $user->id) {
            abort(403, 'Invalid attempt');
        }

        // Проверяем, что попытка еще активна
        if ($attempt->status === QuizAttempt::STATUS_COMPLETED) {
            abort(400, 'This attempt has already been completed');
        }

        // Обрабатываем ответы
        $answers = $request->input('answers', []);
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($answers as $questionId => $answerData) {
            $isCorrect = $this->checkAnswer($questionId, $answerData);

            if ($isCorrect) {
                $correctAnswers++;
            }

            // Сохраняем ответ (здесь нужно использовать вашу модель для ответов)
            // Answer::create([
            //     'student_id' => $user->id,
            //     'question_id' => $questionId,
            //     'option_id' => is_array($answerData) ? null : $answerData,
            //     'answer_text' => is_array($answerData) ? json_encode($answerData) : null,
            //     'is_correct' => $isCorrect,
            //     'created_at' => now()
            // ]);
        }

        // Рассчитываем результат
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // Обновляем попытку
        $attempt->update([
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'completed_at' => now(),
            'time_taken' => now()->diffInSeconds($attempt->started_at),
            'status' => QuizAttempt::STATUS_COMPLETED
        ]);

        return redirect()->route('frontend.quizzes.results', [
            'locale' => $locale,
            'courseId' => $courseId,
            'lessonId' => $lessonId,
            'quizId' => $quizId,
            'attemptId' => $attemptId
        ]);

    } catch (\Exception $e) {
        abort(404, 'Error submitting quiz: ' . $e->getMessage());
    }
}


    private function checkAnswer($questionId, $answerData)
    {
        $question = Question::with('options')->find($questionId);

        if (!$question) {
            return false;
        }

        switch ($question->type) {
            case 'multiple_choice':
                $selectedOption = \App\Models\Option::find($answerData);
                return $selectedOption && $selectedOption->is_correct;

            case 'true_false':
                $correctAnswer = $question->correct_answer;
                return $answerData === $correctAnswer;

            case 'short_answer':
                $correctAnswer = strtolower(trim($question->correct_answer));
                $userAnswer = strtolower(trim($answerData));
                return $userAnswer === $correctAnswer;

            default:
                return false;
        }
    }
}
