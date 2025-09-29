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
                            <div class="h4">{{ $attempt->time_taken_formatted }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">{{ __('Attempt') }}</div>
                            <div class="h4">#{{ $attempt->id }}</div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">{{ __('Question Review') }}</h5>

                    @foreach($attempt->answers as $answerIndex => $answer)
                        @php
                            $question = $answer->question;
                            $questionContent = $question->getTranslation($locale, 'content') ?? $question->content ?? __('Question #:number', ['number' => $answerIndex + 1]);
                        @endphp

                        <div class="card mb-3 border-{{ $answer->is_correct ? 'success' : 'danger' }}">
                            <div class="card-header bg-{{ $answer->is_correct ? 'success' : 'danger' }} text-white">
                                <h6 class="mb-0">
                                    {{ __('Question :number', ['number' => $answerIndex + 1]) }}
                                    <span class="badge bg-{{ $answer->is_correct ? 'light text-success' : 'light text-danger' }} ms-2">
                                        {{ $answer->is_correct ? __('Correct') : __('Incorrect') }}
                                    </span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">{!! nl2br(e($questionContent)) !!}</h6>

                                {{-- Display user's answer --}}
                                <div class="mb-3">
                                    <strong>{{ __('Your answer') }}:</strong>
                                    @if($question->type === 'single' && $answer->option)
                                        @php
                                            $optionText = $answer->option->getTranslation($locale, 'text') ?? $answer->option->text ?? __('Option :key', ['key' => strtoupper($answer->option->key)]);
                                        @endphp
                                        <span class="badge bg-primary">{{ strtoupper($answer->option->key) }}</span> {{ $optionText }}
                                    @elseif($question->type === 'multiple' && $answer->option)
                                        {{-- Для multiple нужно будет доработать модель --}}
                                        <span class="text-muted">{{ __('Multiple choice answer') }}</span>
                                    @elseif($question->type === 'text' && $answer->text_answer)
                                        <div class="alert alert-light border mt-2">
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
                                    <div class="alert alert-info">
                                        <strong>{{ __('Correct answer') }}:</strong>
                                        @if($question->type === 'single')
                                            @php
                                                $correctOption = $question->correctOptions->first();
                                                if ($correctOption) {
                                                    $correctOptionText = $correctOption->getTranslation($locale, 'text') ?? $correctOption->text ?? __('Option :key', ['key' => strtoupper($correctOption->key)]);
                                                    echo '<span class="badge bg-success">' . strtoupper($correctOption->key) . '</span> ' . $correctOptionText;
                                                }
                                            @endphp
                                        @elseif($question->type === 'multiple')
                                            @php
                                                $correctOptions = $question->correctOptions;
                                                $correctOptionsText = [];
                                                foreach ($correctOptions as $correctOption) {
                                                    $correctOptionsText[] = '<span class="badge bg-success">' . strtoupper($correctOption->key) . '</span> ' .
                                                        ($correctOption->getTranslation($locale, 'text') ?? $correctOption->text ?? __('Option :key', ['key' => strtoupper($correctOption->key)]));
                                                }
                                                echo implode(', ', $correctOptionsText);
                                            @endphp
                                        @elseif($question->type === 'text')
                                            {{ $question->correct_option ?? __('Text answer') }}
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

<style>
.rating-stars {
    font-size: 1.2em;
}
</style>
@endsection
