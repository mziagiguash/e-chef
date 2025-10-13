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
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
public function show($locale, $course, $lesson)
{
    try {
        \Log::debug('Quiz show method', [
            'course' => $course,
            'lesson' => $lesson
        ]);

        // Преобразуем ID в модели если нужно
        if (!$course instanceof Course) {
            $course = Course::findOrFail($course);
        }

        if (!$lesson instanceof Lesson) {
            $lesson = Lesson::findOrFail($lesson);
        }

        // Проверяем принадлежность
        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение урока
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

        // Получаем текущего студента
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        // Количество попыток
        $attemptsCount = $quiz->attempts()
            ->where('student_id', $student->id)
            ->count();

        $canAttempt = $quiz->max_attempts === 0 || $attemptsCount < $quiz->max_attempts;

        // 🔴 ДОБАВЛЯЕМ $student в compact
        return view('frontend.quizzes.show', compact(
            'quiz', 'course', 'lesson', 'attemptsCount', 'canAttempt', 'locale', 'student'
        ));

    } catch (\Exception $e) {
        \Log::error('Quiz show error: ' . $e->getMessage());
        abort(404, 'Quiz not found');
    }
}

public function start(Request $request, $locale, $course, $lesson)
{
    try {
        // Получаем текущего студента
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        // Проверяем принадлежность
        if (!$lesson instanceof Lesson) {
            $lesson = Lesson::findOrFail($lesson);
        }
        if (!$course instanceof Course) {
            $course = Course::findOrFail($course);
        }

        if ($lesson->course_id != $course->id) {
            abort(404, 'Lesson does not belong to this course');
        }

        // Получаем квиз через отношение
        $quiz = $lesson->quiz;
        if (!$quiz) {
            abort(404, 'Quiz not found for this lesson');
        }

        // 🔴 ИСПРАВЛЕНО: student_id вместо user_id
        $attemptsCount = $quiz->attempts()
            ->where('student_id', $student->id)
            ->count();

        if ($quiz->max_attempts > 0 && $attemptsCount >= $quiz->max_attempts) {
            return redirect()->back()
                ->with('error', __('You have reached the maximum number of attempts for this quiz.'));
        }

        // Создаем новую попытку
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id, // 🔴 ИСПРАВЛЕНО
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


public function attempt($locale, $course, $lesson, $attemptId)
{
    try {
        // Получаем текущего студента
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        // Проверяем принадлежность
        if (!$lesson instanceof Lesson) {
            $lesson = Lesson::findOrFail($lesson);
        }
        if (!$course instanceof Course) {
            $course = Course::findOrFail($course);
        }

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
            ->where('student_id', $student->id)
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

        // 🔴 ДОБАВЛЯЕМ $student в compact
        return view('frontend.quizzes.attempt', compact(
            'quiz', 'course', 'lesson', 'attempt', 'questions', 'locale', 'student'
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
            // Получаем текущего студента
            $student = $this->getCurrentStudent();

            if (!$student) {
                return redirect()->back()->with('error', 'Student not found');
            }

            // Проверяем принадлежность
            if ($lesson->course_id != $course->id) {
                abort(404, 'Lesson does not belong to this course');
            }

            // Получаем квиз через отношение
            $quiz = $lesson->quiz;
            if (!$quiz) {
                abort(404, 'Quiz not found for this lesson');
            }

            // Находим попытку (ИСПРАВЛЕНО: student_id вместо user_id)
            $attempt = QuizAttempt::where('id', $attemptId)
                ->where('quiz_id', $quiz->id)
                ->where('student_id', $student->id) // ← ИСПРАВЛЕНО
                ->firstOrFail();

            // Проверяем, что попытка еще не завершена
            if ($attempt->status === 'completed') {
                return redirect()->route('frontend.quizzes.results', [
                    'locale' => $locale,
                    'course' => $course->id,
                    'lesson' => $lesson->id,
                    'attempt' => $attempt->id
                ]);
            }

            // Получаем ответы из формы
            $answers = $request->input('answers', []);
            $totalQuestions = $quiz->questions()->count();
            $correctAnswers = 0;

            // Обрабатываем каждый ответ
            foreach ($answers as $questionId => $answerData) {
                $question = Question::find($questionId);

                if (!$question) continue;

                $isCorrect = false;
                $pointsEarned = 0;

                // Обработка в зависимости от типа вопроса
                switch ($question->type) {
                    case 'single':
                        $isCorrect = $this->checkSingleAnswer($question, $answerData);
                        break;

                    case 'multiple':
                        $isCorrect = $this->checkMultipleAnswer($question, $answerData);
                        break;

                    case 'text':
                        $isCorrect = $this->checkTextAnswer($question, $answerData);
                        break;

                    case 'rating':
                        $isCorrect = true; // Рейтинговые вопросы всегда считаются правильными
                        break;
                }

                if ($isCorrect) {
                    $correctAnswers++;
                    $pointsEarned = $question->points;
                }

                // Сохраняем ответ (ИСПРАВЛЕНО: без user_id)
                QuestionAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'option_id' => $this->getOptionId($answerData),
                    'text_answer' => $this->getTextAnswer($answerData, $question->type),
                    'rating_answer' => $this->getRatingAnswer($answerData, $question->type),
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
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
                'status' => 'completed',
            ]);

            // Перенаправляем на страницу результатов
            return redirect()->route('frontend.quizzes.results', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'attempt' => $attempt->id
            ]);

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

 public function results($locale, $course, $lesson, $attemptId)
{
    try {
        // Получаем текущего студента
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        // Проверяем принадлежность
        if (!$lesson instanceof Lesson) {
            $lesson = Lesson::findOrFail($lesson);
        }
        if (!$course instanceof Course) {
            $course = Course::findOrFail($course);
        }

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
            ->where('student_id', $student->id)
            ->with('quiz')
            ->firstOrFail();

        // Загружаем ответы с вопросами
        $answers = QuestionAnswer::with([
            'question.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'option.translations' => function($q) use ($locale) {
                $q->where('locale', $locale);
            }
        ])->where('attempt_id', $attempt->id)
          ->get();

        // 🔴 ДОБАВЛЯЕМ переменную $passed
        $passed = $attempt->score >= $quiz->passing_score;

        return view('frontend.quizzes.results', compact(
            'quiz', 'course', 'lesson', 'attempt', 'answers', 'passed', 'locale', 'student'
        ));

    } catch (\Exception $e) {
        \Log::error('Quiz results error: ' . $e->getMessage());
        return redirect()->route('frontend.quizzes.show', [
            'locale' => $locale,
            'course' => $course->id,
            'lesson' => $lesson->id
        ])->with('error', __('Results not found'));
    }
}

    /**
     * Получает текущего студента
     */
private function getCurrentStudent()
{
    $studentId = session('student_id');

    // Если нет student_id в сессии, пробуем получить из userId
    if (!$studentId && session('userId')) {
        $studentId = encryptor('decrypt', session('userId'));
        if ($studentId) {
            session(['student_id' => $studentId]);
        }
    }

    if ($studentId) {
        return Student::find($studentId);
    }

    return null;
}



    // Вспомогательные методы для проверки ответов
    private function checkSingleAnswer(Question $question, $answerData): bool
    {
        $selectedOption = Option::find($answerData);
        return $selectedOption && $selectedOption->is_correct;
    }

    private function checkMultipleAnswer(Question $question, $answerData): bool
    {
        if (!is_array($answerData)) return false;

        $selectedOptions = Option::whereIn('id', $answerData)->get();
        $correctOptions = $question->options()->where('is_correct', true)->get();

        // Все выбранные должны быть правильными и все правильные должны быть выбраны
        return $selectedOptions->count() === $correctOptions->count() &&
               $selectedOptions->every(function($option) { return $option->is_correct; });
    }

    private function checkTextAnswer(Question $question, $answerData): bool
    {
        // Для текстовых вопросов можно добавить сложную логику проверки
        // Пока просто возвращаем true для демонстрации
        return !empty(trim($answerData));
    }

    private function getOptionId($answerData)
    {
        if (is_array($answerData)) {
            return null; // Для множественного выбора не сохраняем option_id
        }
        return is_numeric($answerData) ? $answerData : null;
    }

    private function getTextAnswer($answerData, $questionType)
    {
        return $questionType === 'text' ? $answerData : null;
    }

    private function getRatingAnswer($answerData, $questionType)
    {
        return $questionType === 'rating' && is_numeric($answerData) ? (int)$answerData : null;
    }
}
