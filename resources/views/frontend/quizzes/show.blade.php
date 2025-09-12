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
                <a href="{{ route('frontend.watchCourse', [$locale, $course->id]) }}">
                    {{ $course->translations->firstWhere('locale', $locale)->title ?? $course->title ?? 'Course' }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}">
                    {{ $lesson->translations->firstWhere('locale', $locale)->title ?? $lesson->title ?? 'Lesson' }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->title ?? 'Quiz' }}
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
                        {{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->title ?? 'Quiz' }}
                    </h1>
                </div>
                <div class="card-body">
                    @php
                        $quizTranslation = $quiz->translations->firstWhere('locale', $locale);
                    @endphp

                    @if($quizTranslation && $quizTranslation->description)
                        <p class="lead">{{ $quizTranslation->description }}</p>
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
                {{-- Start Quiz Button --}}
                <div class="card">
                    <div class="card-body text-center">
                        <h4>{{ __('Ready to take the quiz?') }}</h4>
                        <p class="text-muted">{{ __('This quiz contains :count questions', ['count' => $quiz->questions->count()]) }}</p>

<form action="/{{ $locale }}/courses/{{ $course->id }}/lessons/{{ $lesson->id }}/quizzes/{{ $quiz->id }}/start" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-play me-2"></i> {{ __('Start Quiz') }}
    </button>
</form>
                    </div>
                </div>

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
{{-- Стили для радио-кнопок --}}
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

        if (timeLeft <= 0) {
            clearInterval(timer);
        }
    }, 1000);
});
</script>
@endif

@endsection
