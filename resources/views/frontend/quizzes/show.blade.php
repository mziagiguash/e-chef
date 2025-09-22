@extends('frontend.layouts.app')

@section('title', __('Take Quiz'))

@section('content')
<div class="container py-5">

    {{-- Хлебные крошки --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url("/$locale") }}">
                    {{ __('Home') }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('frontend.courses.show', [$locale, $course->id]) }}">
                    {{ $course->translations->firstWhere('locale', $locale)->title ?? $course->translations->first()->title ?? $course->title ?? 'Course' }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}">
                    {{ $lesson->translations->firstWhere('locale', $locale)->title ?? $lesson->translations->first()->title ?? $lesson->title ?? 'Lesson' }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->translations->first()->title ?? $quiz->title ?? 'Quiz' }}
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Lesson') }}
                </a>
            </div>

            {{-- Quiz Header --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->translations->first()->title ?? $quiz->title ?? 'Quiz' }}
                    </h1>
                </div>
                <div class="card-body">
                    @php
                        $quizTranslation = $quiz->translations->firstWhere('locale', $locale);
                    @endphp

                    @if($quizTranslation && $quizTranslation->description)
                        <p class="lead">{{ $quizTranslation->description }}</p>
                    @elseif($quiz->translations->first() && $quiz->translations->first()->description)
                        <p class="lead">{{ $quiz->translations->first()->description }}</p>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <strong><i class="fas fa-clock me-2"></i> {{ __('Time Limit') }}:</strong>
                            {{ $quiz->time_limit ? $quiz->time_limit . ' ' . __('minutes') : __('No limit') }}
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-trophy me-2"></i> {{ __('Passing Score') }}:</strong>
                            {{ $quiz->passing_score }}%
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-redo me-2"></i> {{ __('Attempts') }}:</strong>
                            {{ $attemptsCount }}/{{ $quiz->max_attempts > 0 ? $quiz->max_attempts : '∞' }}
                        </div>
                    </div>

                    @if(!$canAttempt)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('You have reached the maximum number of attempts for this quiz.') }}
                        </div>
                    @endif
                </div>
            </div>

            @if($canAttempt)
                {{-- Quiz Questions Form --}}
                <form action="{{ route('frontend.quizzes.start', [
                    'locale' => $locale,
                    'course' => $course->id,
                    'lesson' => $lesson->id,
                    'quiz' => $quiz->id
                ]) }}" method="POST" id="quiz-form">
                    @csrf

                    {{-- Timer --}}
                    @if($quiz->time_limit)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-clock me-2"></i>
                        {{ __('Time remaining') }}:
                        <span id="timer">{{ $quiz->time_limit }}:00</span>
                    </div>
                    @endif

                    {{-- Questions --}}
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $questionTranslation = $question->translations->firstWhere('locale', $locale);
                            $questionText = $questionTranslation->question_text ?? $questionTranslation->content ?? $question->translations->first()->question_text ?? $question->translations->first()->content ?? $question->question_text ?? 'Question';
                        @endphp

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    {{ __('Question') }} {{ $index + 1 }}: {{ $questionText }}
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($questionTranslation && ($questionTranslation->explanation || $questionTranslation->hint))
                                    <p class="text-muted mb-3">
                                        <small>{{ $questionTranslation->explanation ?? $questionTranslation->hint }}</small>
                                    </p>
                                @endif

                                <div class="options-list">
                                    @switch($question->type)
                                        @case('single')
                                            @foreach($question->options as $option)
                                                @php
                                                    $optionTranslation = $option->translations->firstWhere('locale', $locale);
                                                    $optionText = $optionTranslation->option_text ?? $option->translations->first()->option_text ?? $option->option_text ?? 'Option';
                                                @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           id="option_{{ $option->id }}"
                                                           value="{{ $option->id }}" required>
                                                    <label class="form-check-label" for="option_{{ $option->id }}">
                                                        {{ $optionText }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @break

                                        @case('multiple')
                                            @foreach($question->options as $option)
                                                @php
                                                    $optionTranslation = $option->translations->firstWhere('locale', $locale);
                                                    $optionText = $optionTranslation->option_text ?? $option->translations->first()->option_text ?? $option->option_text ?? 'Option';
                                                @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="answers[{{ $question->id }}][]"
                                                           id="option_{{ $option->id }}"
                                                           value="{{ $option->id }}">
                                                    <label class="form-check-label" for="option_{{ $option->id }}">
                                                        {{ $optionText }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @break

                                        @case('true_false')
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                       name="answers[{{ $question->id }}]"
                                                       id="true_{{ $question->id }}"
                                                       value="true" required>
                                                <label class="form-check-label" for="true_{{ $question->id }}">
                                                    {{ __('True') }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                       name="answers[{{ $question->id }}]"
                                                       id="false_{{ $question->id }}"
                                                       value="false" required>
                                                <label class="form-check-label" for="false_{{ $question->id }}">
                                                    {{ __('False') }}
                                                </label>
                                            </div>
                                            @break

                                        @case('text')
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3"
                                                          name="answers[{{ $question->id }}]"
                                                          placeholder="{{ __('Your answer') }}" required></textarea>
                                            </div>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Submit Button --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> {{ __('Submit Quiz') }}
                        </button>
                    </div>
                </form>
            @else
                {{-- Results Link --}}
                <div class="text-center">
                    <a href="{{ route('frontend.quizzes.results', [
                        'locale' => $locale,
                        'course' => $course->id,
                        'lesson' => $lesson->id,
                        'quiz' => $quiz->id,
                        'attempt' => $quiz->attempts()->where('user_id', auth()->id())->latest()->first()->id ?? 0
                    ]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-2"></i> {{ __('View Previous Results') }}
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
.form-check-input {
    width: 20px !important;
    height: 20px !important;
    margin-right: 10px !important;
}

.form-check-label {
    font-size: 16px !important;
    vertical-align: middle !important;
}

.options-list .form-check {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 8px;
    background-color: #f9f9f9;
}

.options-list .form-check:hover {
    background-color: #e9ecef;
    border-color: #007bff;
}
</style>

@if($quiz->time_limit)
<script>
// Timer functionality
document.addEventListener('DOMContentLoaded', function() {
    const timerElement = document.getElementById('timer');
    let timeLeft = {{ $quiz->time_limit * 60 }};

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft <= 0) {
            clearInterval(timer);
            document.getElementById('quiz-form').submit();
        }

        if (timeLeft <= 60) {
            timerElement.parentElement.classList.add('alert-danger');
            timerElement.parentElement.classList.remove('alert-info');
        }
    }

    updateTimer(); // Initial update

    const timer = setInterval(function() {
        timeLeft--;
        updateTimer();
    }, 1000);
});
</script>
@endif

@endsection
