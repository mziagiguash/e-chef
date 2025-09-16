<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionAnswer;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class AnswerSeeder extends Seeder
{
    public function run()
    {
        $attempts = QuizAttempt::with(['quiz.questions.options.translations'])->get();

        if ($attempts->isEmpty()) {
            $this->command->info('No quiz attempts found! Please run QuizAttemptSeeder first.');
            return;
        }

        foreach ($attempts as $attempt) {
            $questions = $attempt->quiz->questions;

            foreach ($questions as $question) {
                $isCorrect = (bool) rand(0, 1); // 50% chance

                $answerData = [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'user_id' => $attempt->user_id,
                    'is_correct' => $isCorrect,
                    'points_earned' => $isCorrect ? ($question->points ?? 1) : 0,
                    'text_answer' => null,
                    'rating_answer' => null,
                ];

                switch ($question->type) {
                    case 'single':
                    case 'multiple':
                        if ($question->options->isNotEmpty()) {
                            $correctOptions = $question->options->where('is_correct', true);
                            $incorrectOptions = $question->options->where('is_correct', false);

                            if ($isCorrect && $correctOptions->isNotEmpty()) {
                                $selectedOption = $correctOptions->random();
                                $answerData['text_answer'] = $this->getOptionText($selectedOption);
                            } elseif ($incorrectOptions->isNotEmpty()) {
                                $selectedOption = $incorrectOptions->random();
                                $answerData['text_answer'] = $this->getOptionText($selectedOption);
                            } else {
                                $answerData['text_answer'] = 'No option selected';
                            }
                        } else {
                            $answerData['text_answer'] = 'No options available';
                        }
                        break;

                    case 'text':
                        $answerData['text_answer'] = $isCorrect ?
                            'Correct text answer' :
                            fake()->sentence(3);
                        break;

                    case 'rating':
                        $minRating = $question->min_rating ?? 1;
                        $maxRating = $question->max_rating ?? 10;
                        $answerData['rating_answer'] = $isCorrect ?
                            $maxRating :
                            rand($minRating, $maxRating - 1);
                        break;

                    default:
                        $answerData['text_answer'] = 'Default answer';
                }

                QuestionAnswer::create($answerData);
            }
        }

        $this->command->info('Question answers seeded successfully! Created: ' . QuestionAnswer::count() . ' answers');
    }

    private function getOptionText(Option $option): string
    {
        // Загружаем переводы если еще не загружены
        if (!$option->relationLoaded('translations')) {
            $option->load('translations');
        }

        // Получаем текст опции на английском или первый доступный перевод
        $translation = $option->translations->firstWhere('locale', 'en') ??
                      $option->translations->first();

        return $translation->option_text ?? 'Option text not available';
    }
}
