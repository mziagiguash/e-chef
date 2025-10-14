@extends('frontend.layouts.app')

@section('title', __('Quiz Results'))

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url("/$locale") }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('frontend.courses.show', [$locale, $course->id]) }}">{{ $course->getTranslation($locale, 'title') ?? $course->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}">{{ $lesson->getTranslation($locale, 'title') ?? $lesson->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('frontend.quizzes.show', [$locale, $course->id, $lesson->id]) }}">
                {{ $quiz->getTranslation($locale, 'title') ?? $quiz->title }}
            </a></li>
            <li class="breadcrumb-item active">{{ __('Results') }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-{{ $passed ? 'success' : 'danger' }} text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-{{ $passed ? 'check-circle' : 'times-circle' }} me-2"></i>
                        {{ $passed ? __('Quiz Passed!') : __('Quiz Failed') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold text-{{ $passed ? 'success' : 'danger' }}">
                            {{ $attempt->score }}%
                        </div>
                        <p class="text-muted">{{ __('Passing Score: :score%', ['score' => $quiz->passing_score]) }}</p>
                    </div>

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="fw-bold">{{ __('Correct Answers') }}</div>
                            <div class="h4">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">{{ __('Time Taken') }}</div>
                            <div class="h4">
                                @php
                                    $minutes = floor($attempt->time_taken / 60);
                                    $seconds = $attempt->time_taken % 60;
                                    echo $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
                                @endphp
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">{{ __('Attempt') }}</div>
                            <div class="h4">#{{ $attempt->id }}</div>
                        </div>
                    </div>

                    <hr>

                    {{-- Question Review --}}
                    @foreach($answers as $answerIndex => $answer)
                        @php
                            $question = $answer->question;
                            $questionTranslation = $question->translations->where('locale', $locale)->first();
                            $questionContent = $questionTranslation->content ?? $question->translations->first()->content ?? $question->content ?? __('Question #:number', ['number' => $answerIndex + 1]);
                        @endphp

                        <div class="card mb-3 border-{{ $answer->is_correct ? 'success' : 'danger' }}">
                            <div class="card-header bg-{{ $answer->is_correct ? 'success' : 'danger' }} text-white d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold">{{ __('Question :number', ['number' => $answerIndex + 1]) }}</h6>
                                <span class="quiz-header-badge {{ $answer->is_correct ? 'quiz-header-badge-correct' : 'quiz-header-badge-incorrect' }}">
                                    <i class="fas {{ $answer->is_correct ? 'fa-check' : 'fa-times' }} me-1"></i>
                                    {{ $answer->is_correct ? __('Correct') : __('Incorrect') }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">{!! nl2br(e($questionContent)) !!}</h6>

{{-- Display user's answer --}}
<div class="mb-3">
    <strong>{{ __('Your answer') }}:</strong>
    @if($question->type === 'single' && $answer->option)
        @php
            $optionTranslation = $answer->option->translations->where('locale', $locale)->first();
            $optionText = $optionTranslation->text ?? $answer->option->translations->first()->text ?? $answer->option->text ?? __('Option :key', ['key' => strtoupper($answer->option->key)]);
        @endphp
        <span class="quiz-badge quiz-badge-option">{{ strtoupper($answer->option->key) }}</span>
        <span class="{{ $answer->is_correct ? 'text-success fw-bold' : 'text-danger' }}">{{ $optionText }}</span>
    @elseif($question->type === 'multiple')
        {{-- 🔴 ИСПРАВЛЕНИЕ: Используем selectedOptions вместо option --}}
        @if($answer->selectedOptions->count() > 0)
            <div class="user-multiple-answers mt-2">
                @foreach($answer->selectedOptions as $selectedOption)
                    @php
                        $optionTranslation = $selectedOption->translations->where('locale', $locale)->first();
                        $optionText = $optionTranslation->text ?? $selectedOption->translations->first()->text ?? $selectedOption->text ?? __('Option :key', ['key' => strtoupper($selectedOption->key)]);
                    @endphp
                    <div class="d-flex align-items-center mb-1">
                        <span class="quiz-badge quiz-badge-option me-2">{{ strtoupper($selectedOption->key) }}</span>
                        <span class="{{ $selectedOption->is_correct ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                            {{ $optionText }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-danger">{{ __('No answer provided') }}</span>
        @endif
    @elseif($question->type === 'text' && $answer->text_answer)
        <div class="alert alert-light border mt-2 {{ $answer->is_correct ? 'border-success' : 'border-danger' }}">
            {{ $answer->text_answer }}
        </div>
    @elseif($question->type === 'rating' && $answer->rating_answer)
        <div class="rating-stars">
            @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $answer->rating_answer ? 'text-warning' : 'text-muted' }}"></i>
            @endfor
            <span class="ms-2">({{ $answer->rating_answer }}/5)</span>
        </div>
    @else
        <span class="text-danger">{{ __('No answer provided') }}</span>
    @endif
</div>

                                {{-- Display correct answer for incorrect responses --}}
                                @if(!$answer->is_correct)
                                    <div class="alert alert-success border-0">
                                        <strong>{{ __('Correct answer') }}:</strong>
                                        @if($question->type === 'single')
                                            @php
                                                $correctOption = $question->options->where('is_correct', true)->first();
                                                if ($correctOption) {
                                                    $correctOptionTranslation = $correctOption->translations->where('locale', $locale)->first();
                                                    $correctOptionText = $correctOptionTranslation->text ?? $correctOption->translations->first()->text ?? $correctOption->text ?? __('Option :key', ['key' => strtoupper($correctOption->key)]);
                                                    echo '<span class="quiz-badge quiz-badge-correct-option">' . strtoupper($correctOption->key) . '</span> <span class="text-success fw-bold">' . $correctOptionText . '</span>';
                                                }
                                            @endphp
                                        @elseif($question->type === 'multiple')
                                            @php
                                                $correctOptions = $question->options->where('is_correct', true);
                                                $correctOptionsText = [];
                                                foreach ($correctOptions as $correctOption) {
                                                    $correctOptionTranslation = $correctOption->translations->where('locale', $locale)->first();
                                                    $correctOptionText = $correctOptionTranslation->text ?? $correctOption->translations->first()->text ?? $correctOption->text ?? __('Option :key', ['key' => strtoupper($correctOption->key)]);
                                                    $correctOptionsText[] = '<span class="quiz-badge quiz-badge-correct-option">' . strtoupper($correctOption->key) . '</span> ' . $correctOptionText;
                                                }
                                                echo implode(', ', $correctOptionsText);
                                            @endphp
                                        @elseif($question->type === 'text')
                                            <span class="text-success fw-bold">{{ $question->correct_answer ?? __('Text answer') }}</span>
                                        @elseif($question->type === 'rating')
                                            {{ __('Rating question') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Navigation Buttons --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('frontend.quizzes.show', [
                            'locale' => $locale,
                            'course' => $course->id,
                            'lesson' => $lesson->id
                        ]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Quiz') }}
                        </a>

                        <a href="{{ route('lessons.show', [
                            'locale' => $locale,
                            'course' => $course->id,
                            'lesson' => $lesson->id
                        ]) }}" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i> {{ __('Continue to Next Lesson') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.quiz-header-badge {
    padding: 0.5em 1em;
    font-size: 0.8em;
    font-weight: 700;
    border-radius: 0.5rem;
    border: 2px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
}

.quiz-header-badge-correct {
    color: #198754;
}

.quiz-header-badge-incorrect {
    color: #dc3545;
}

.rating-stars {
    font-size: 1.2em;
}

.quiz-badge {
    padding: 0.5em 0.75em;
    font-size: 0.75em;
    font-weight: 600;
    border-radius: 0.375rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.quiz-badge-option {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    min-width: 2rem;
    text-align: center;
}

.quiz-badge-correct-option {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
    min-width: 2rem;
    text-align: center;
}

/* Стили для правильных ответов */
.border-success {
    border-color: #198754 !important;
}

.bg-success {
    background-color: #198754 !important;
}

.text-success {
    color: #198754 !important;
}

/* Стили для неправильных ответов */
.border-danger {
    border-color: #dc3545 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.text-danger {
    color: #dc3545 !important;
}
</style>
@endpush
