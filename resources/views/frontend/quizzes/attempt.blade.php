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
                    'lesson' => $lesson->id,
                    'quiz' => $quiz->id
                ]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Back') }}
                </a>
            </div>

            {{-- Quiz Header --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->title }}
                    </h1>
                </div>
                <div class="card-body">
                    @php
                        $quizTranslation = $quiz->translations->firstWhere('locale', $locale);
                    $timeLimit = $quiz->time_limit ? $quiz->time_limit . ' ' . __('minutes') : __('No limit');
                    $passingScore = $quiz->passing_score . '%';
                    $currentAttempt = $quiz->attempts()->where('user_id', auth()->id())->count() + 1;
                        $maxAttempts = $quiz->max_attempts > 0 ? $quiz->max_attempts : '∞';
                    @endphp

                    @if($quizTranslation && $quizTranslation->description)
                        <p class="lead">{{ $quizTranslation->description }}</p>
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
                            {{ $currentAttempt }}/{{ $maxAttempts }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quiz Form --}}
            <form action="{{ route('frontend.quizzes.submit', [
                'locale' => $locale,
                'course' => $course->id,
                'lesson' => $lesson->id,
                'quiz' => $quiz->id,
                'attempt' => $attempt->id
            ]) }}" method="POST" id="quiz-form">
                @csrf

                {{-- Questions --}}
                @foreach($quiz->questions as $index => $question)
                    <div class="card mb-4 question-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ __('Question') }} {{ $index + 1 }} {{ __('of') }} {{ $quiz->questions->count() }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $questionTranslation = $question->translations->firstWhere('locale', $locale);
                                $questionText = $questionTranslation ? $questionTranslation->question_text : ($question->translations->first() ? $question->translations->first()->question_text : __('No question content'));
                            @endphp

                            <h6 class="card-title mb-3">{{ $questionText }}</h6>

                            {{-- Options --}}
                            <div class="options-list">
                                @if($question->type === 'multiple_choice')
                                    @if($question->options->count() > 0)
                                        @foreach($question->options as $option)
                                            @php
                                                $optionTranslation = $option->translations->firstWhere('locale', $locale);
                                                $optionText = $optionTranslation ? $optionTranslation->option_text : ($option->translations->first() ? $option->translations->first()->option_text : __('No option text'));
                                            @endphp

                                            <div class="form-check mb-3 p-3 option-item">
                                                <input class="form-check-input" type="radio"
                                                       name="answers[{{ $question->id }}]"
                                                       value="{{ $option->id }}"
                                                       id="option_{{ $option->id }}_{{ $question->id }}"
                                                       required>
                                                <label class="form-check-label w-100" for="option_{{ $option->id }}_{{ $question->id }}">
                                                    {{ $optionText }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-danger">
                                            {{ __('No options available for this question!') }}
                                        </div>
                                    @endif

                                @elseif($question->type === 'true_false')
                                    <div class="form-check mb-3 p-3 option-item">
                                        <input class="form-check-input" type="radio"
                                               name="answers[{{ $question->id }}]"
                                               value="true"
                                               id="true_{{ $question->id }}"
                                               required>
                                        <label class="form-check-label w-100" for="true_{{ $question->id }}">
                                            {{ __('True') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-3 p-3 option-item">
                                        <input class="form-check-input" type="radio"
                                               name="answers[{{ $question->id }}]"
                                               value="false"
                                               id="false_{{ $question->id }}"
                                               required>
                                        <label class="form-check-label w-100" for="false_{{ $question->id }}">
                                            {{ __('False') }}
                                        </label>
                                    </div>

                                @elseif($question->type === 'short_answer')
                                    <div class="form-group">
                                        <label for="answer_{{ $question->id }}" class="form-label">{{ __('Your answer') }}:</label>
                                        <input type="text"
                                               class="form-control form-control-lg"
                                               name="answers[{{ $question->id }}]"
                                               id="answer_{{ $question->id }}"
                                               required
                                               placeholder="{{ __('Type your answer here') }}">
                                    </div>
                                @endif
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
