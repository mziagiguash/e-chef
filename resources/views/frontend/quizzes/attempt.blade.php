@extends('frontend.layouts.app')

@section('title', __('Take Quiz'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('frontend.quizzes.show', [
                    'locale' => $locale,
                    'course' => $course->id,
                    'lesson' => $lesson->id
                ]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Back') }}
                </a>
            </div>

            {{-- Quiz Header --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $quiz->getTranslation($locale, 'title') ?? $quiz->title ?? __('Quiz') }}
                    </h1>
                </div>
                <div class="card-body">
                    @php
                        $timeLimit = $quiz->time_limit ? $quiz->time_limit . ' ' . __('minutes') : __('No limit');
                        $passingScore = $quiz->passing_score . '%';
                        $currentAttempt = $quiz->attempts()->where('user_id', auth()->id())->count();
                        $maxAttempts = $quiz->max_attempts > 0 ? $quiz->max_attempts : '∞';
                    @endphp

                    @if($quizDescription = $quiz->getTranslation($locale, 'description'))
                        <p class="lead">{{ $quizDescription }}</p>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <strong><i class="fas fa-clock me-2"></i> {{ __('Time Limit') }}:</strong>
                            {{ $timeLimit }}
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-trophy me-2"></i> {{ __('Passing Score') }}:</strong>
                            {{ $passingScore }}
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-redo me-2"></i> {{ __('Attempt') }}:</strong>
                            {{ $currentAttempt + 1 }}/{{ $maxAttempts }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quiz Form --}}
            <form action="{{ route('frontend.quizzes.submit', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'attempt' => $attempt->id
            ]) }}" method="POST" id="quiz-form">
                @csrf

                {{-- Questions --}}
                @foreach($questions as $index => $question)
                    <div class="card mb-4 question-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ __('Question') }} {{ $index + 1 }} {{ __('of') }} {{ $questions->count() }}
                                @if($question->points > 1)
                                    <span class="badge bg-primary ms-2">{{ $question->points }} {{ __('points') }}</span>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $questionContent = $question->getTranslation($locale, 'content') ?? $question->content ?? __('No question content');
                            @endphp

                            <h6 class="card-title mb-3">{!! nl2br(e($questionContent)) !!}</h6>

                            {{-- Options --}}
                            <div class="options-list">
                                @switch($question->type)
                                    @case('single')
                                        @if($question->options->count() > 0)
                                            @foreach($question->options as $option)
                                                @php
                                                    $optionText = $option->getTranslation($locale, 'text') ?? $option->text ?? __('No option text');
                                                @endphp

                                                <div class="form-check mb-3 p-3 option-item">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ $option->key }}"
                                                           id="option_{{ $option->id }}_{{ $question->id }}"
                                                           required>
                                                    <label class="form-check-label w-100" for="option_{{ $option->id }}_{{ $question->id }}">
                                                        <strong>{{ strtoupper($option->key) }}.</strong> {{ $optionText }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                {{ __('No options available for this question!') }}
                                            </div>
                                        @endif
                                        @break

                                    @case('multiple')
                                        @if($question->options->count() > 0)
                                            @foreach($question->options as $option)
                                                @php
                                                    $optionText = $option->getTranslation($locale, 'text') ?? $option->text ?? __('No option text');
                                                @endphp

                                                <div class="form-check mb-3 p-3 option-item">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="answers[{{ $question->id }}][]"
                                                           value="{{ $option->key }}"
                                                           id="option_{{ $option->id }}_{{ $question->id }}">
                                                    <label class="form-check-label w-100" for="option_{{ $option->id }}_{{ $question->id }}">
                                                        <strong>{{ strtoupper($option->key) }}.</strong> {{ $optionText }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                {{ __('No options available for this question!') }}
                                            </div>
                                        @endif
                                        @break

                                    @case('text')
                                        <div class="form-group">
                                            <label for="answer_{{ $question->id }}" class="form-label">{{ __('Your answer') }}:</label>
                                            <textarea class="form-control form-control-lg"
                                                   name="answers[{{ $question->id }}]"
                                                   id="answer_{{ $question->id }}"
                                                   required
                                                   rows="3"
                                                   placeholder="{{ __('Type your answer here') }}"></textarea>
                                        </div>
                                        @break

                                    @case('rating')
                                        <div class="form-group">
                                            <label for="rating_{{ $question->id }}" class="form-label">
                                                {{ __('Select rating from :min to :max', ['min' => $question->min_rating ?? 1, 'max' => $question->max_rating ?? 5]) }}:
                                            </label>
                                            <select class="form-control form-control-lg" name="answers[{{ $question->id }}]" required>
                                                <option value="">{{ __('Select rating') }}</option>
                                                @for($i = ($question->min_rating ?? 1); $i <= ($question->max_rating ?? 5); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Submit Button --}}
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i> {{ __('Submit Quiz') }}
                    </button>
                </div>
            </form>

            {{-- Timer --}}
            @if($quiz->time_limit)
                <div class="text-center mt-4">
                    <div class="timer-alert alert alert-info">
                        <i class="fas fa-clock me-2"></i>
                        {{ __('Time remaining') }}: <span id="timer">{{ $quiz->time_limit * 60 }}</span> {{ __('seconds') }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
.option-item {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.option-item:hover {
    border-color: #007bff;
    background-color: #e3f2fd;
    transform: translateY(-2px);
}

.option-item .form-check-input {
    width: 20px;
    height: 20px;
    margin-top: 2px;
}

.option-item .form-check-label {
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
}

.form-control-lg {
    font-size: 16px;
    padding: 12px;
}

.timer-alert {
    font-size: 18px;
    font-weight: bold;
}
</style>

@if($quiz->time_limit)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timerElement = document.getElementById('timer');
    let timeLeft = {{ $quiz->time_limit * 60 }};

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft <= 0) {
            document.getElementById('quiz-form').submit();
        }

        if (timeLeft <= 60) {
            timerElement.parentElement.classList.add('alert-danger');
            timerElement.parentElement.classList.remove('alert-info');
        }
    }

    updateTimer();

    const timer = setInterval(function() {
        timeLeft--;
        updateTimer();

        if (timeLeft <= 0) {
            clearInterval(timer);
        }
    }, 1000);
});
</script>
@endif

@endsection
