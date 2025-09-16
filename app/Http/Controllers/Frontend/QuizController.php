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
    public function show($locale, Course $course, Lesson $lesson, Quiz $quiz)
    {
        try {
            // Проверяем принадлежность урока курсу и квиза уроку
            if ($lesson->course_id != $course->id) {
                abort(404, 'Lesson does not belong to this course');
            }

            if ($quiz->lesson_id != $lesson->id) {
                abort(404, 'Quiz does not belong to this lesson');
            }

            // Загружаем вопросы с опциями и переводами
            $quiz->load([
                'translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                'questions' => function($query) use ($locale) {
                    $query->orderBy('order', 'asc')
                          ->with(['translations' => function($q) use ($locale) {
                              $q->where('locale', $locale);
                          }, 'options' => function($q) use ($locale) {
                              $q->orderBy('order', 'asc')
                                ->with(['translations' => function($q2) use ($locale) {
                                    $q2->where('locale', $locale);
                                }]);
                          }]);
                }
            ]);

            // Получаем количество попыток пользователя
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

    public function start(Request $request, $locale, Course $course, Lesson $lesson, Quiz $quiz)
{
    try {
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

        // Обрабатываем ответы из формы
        $answers = $request->input('answers', []);
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions()->count();

        foreach ($answers as $questionId => $answerData) {
            $question = Question::find($questionId);
            if (!$question) continue;

            $isCorrect = $this->checkAnswer($question, $answerData);

            if ($isCorrect) {
                $correctAnswers++;
            }

            // Сохраняем ответ пользователя в question_answers
            QuestionAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'user_id' => $user->id,
                'option_id' => $question->type === 'multiple_choice' ? $answerData : null,
                'text_answer' => $question->type === 'short_answer' ? $answerData : null,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? 1 : 0
            ]);
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
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $quiz,
            'attempt' => $attempt
        ]);

    } catch (\Exception $e) {
        abort(404, 'Error starting quiz: ' . $e->getMessage());
    }
}

    public function results($locale, Course $course, Lesson $lesson, Quiz $quiz, $attempt = null)
    {
        try {
            // Проверяем принадлежность
            if ($lesson->course_id != $course->id || $quiz->lesson_id != $lesson->id) {
                abort(404, 'Invalid course, lesson or quiz relationship');
            }

            // Если attemptId не передан, берем последнюю попытку
            if (!$attempt) {
                $attempt = $quiz->attempts()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->firstOrFail();
            } else {
                $attempt = QuizAttempt::where('id', $attempt)
                    ->where('quiz_id', $quiz->id)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();
            }

            // Загружаем ответы с вопросами и опциями
            $attempt->load(['answers.question', 'answers.option']);

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

    private function checkAnswer(Question $question, $answerData)
{
    switch ($question->type) {
        case 'multiple_choice':
            $selectedOption = Option::find($answerData);
            return $selectedOption && $selectedOption->is_correct;

        case 'true_false':
            $correctAnswer = $question->correct_answer; // предполагая, что есть поле correct_answer в questions
            return $answerData === $correctAnswer;

        case 'short_answer':
            $correctAnswer = strtolower(trim($question->correct_answer)); // предполагая, что есть поле correct_answer
            $userAnswer = strtolower(trim($answerData));
            return $userAnswer === $correctAnswer;

        default:
            return false;
    }
}
}
