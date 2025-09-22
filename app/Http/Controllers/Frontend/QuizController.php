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
            // Проверяем принадлежность
            if ($lesson->course_id != $course->id) {
                abort(404, 'Lesson does not belong to this course');
            }

            if ($quiz->lesson_id != $lesson->id) {
                abort(404, 'Quiz does not belong to this lesson');
            }

            // Загружаем вопросы с переводами
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

            // Получаем перевод
            $quizTranslation = $quiz->translations->firstWhere('locale', $locale)
                ?? $quiz->translations->first();

            // Количество попыток
            $attemptsCount = $quiz->attempts()
                ->where('user_id', auth()->id())
                ->count();

            $canAttempt = $quiz->max_attempts === 0 || $attemptsCount < $quiz->max_attempts;

            return view('frontend.quizzes.show', compact(
                'quiz', 'course', 'lesson', 'attemptsCount', 'canAttempt', 'locale', 'quizTranslation'
            ));

        } catch (\Exception $e) {
            abort(404, 'Quiz not found');
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
                'quiz' => $quiz->id,
                'attempt' => $attempt->id
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error starting quiz: ') . $e->getMessage());
        }
    }

    public function attempt($locale, Course $course, Lesson $lesson, Quiz $quiz, $attemptId)
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
                    'quiz' => $quiz->id,
                    'attempt' => $attempt->id
                ]);
            }

            // Загружаем вопросы с переводами
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

            return view('frontend.quizzes.attempt', compact(
                'quiz', 'course', 'lesson', 'attempt', 'locale', 'attemptId'
            ));

        } catch (\Exception $e) {
            return redirect()->route('frontend.quizzes.show', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'quiz' => $quiz->id
            ])->with('error', __('Quiz attempt not found'));
        }
    }

    public function submit(Request $request, $locale, Course $course, Lesson $lesson, Quiz $quiz, $attemptId)
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

            // Находим попытку
            $attempt = QuizAttempt::where('id', $attemptId)
                ->where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($attempt->status === 'completed') {
                return redirect()->route('frontend.quizzes.results', [
                    'locale' => $locale,
                    'course' => $course->id,
                    'lesson' => $lesson->id,
                    'quiz' => $quiz->id,
                    'attempt' => $attempt->id
                ]);
            }

            // Обрабатываем ответы
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

                // Сохраняем ответ
                QuestionAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'user_id' => $user->id,
                    'option_id' => $question->type === 'single' ? $answerData : null,
                    'text_answer' => $question->type === 'text' ? $answerData : null,
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
                'status' => 'completed'
            ]);

            return redirect()->route('frontend.quizzes.results', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'quiz' => $quiz->id,
                'attempt' => $attempt->id
            ]);

        } catch (\Exception $e) {
            return redirect()->route('frontend.quizzes.attempt', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'quiz' => $quiz->id,
                'attempt' => $attemptId
            ])->with('error', __('Error submitting quiz'));
        }
    }

    public function results($locale, Course $course, Lesson $lesson, Quiz $quiz, $attemptId)
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

            // Находим попытку
            $attempt = QuizAttempt::where('id', $attemptId)
                ->where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Загружаем ответы с вопросами
            $attempt->load(['answers.question']);

            $passed = $attempt->score >= $quiz->passing_score;

            return view('frontend.quizzes.results', compact(
                'quiz', 'locale', 'passed', 'attempt', 'course', 'lesson'
            ));

        } catch (\Exception $e) {
            return redirect()->route('frontend.quizzes.show', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'quiz' => $quiz->id
            ])->with('error', __('Results not found'));
        }
    }

    private function checkAnswer(Question $question, $answerData)
    {
        switch ($question->type) {
            case 'single':
                $selectedOption = Option::find($answerData);
                return $selectedOption && $selectedOption->is_correct;

            case 'multiple':
                if (!is_array($answerData)) return false;

                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                sort($correctOptions);
                sort($answerData);

                return $correctOptions == $answerData;

            case 'text':
                $correctAnswer = strtolower(trim($question->correct_answer));
                $userAnswer = strtolower(trim($answerData));
                return $userAnswer === $correctAnswer;

            case 'true_false':
                return $answerData == $question->correct_answer;

            default:
                return false;
        }
    }
}
