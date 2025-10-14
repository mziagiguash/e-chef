@extends('frontend.layouts.app')

@section('title', __('Take Quiz'))

@section('content')
<div class="container py-5">
    {{-- Хлебные крошки --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url("/$locale") }}">{{ __('Home') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('frontend.courses.show', [$locale, $course->id]) }}">
                    {{ $course->getTranslation($locale, 'title') ?? $course->title ?? 'Course' }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}">
                    {{ $lesson->getTranslation($locale, 'title') ?? $lesson->title ?? 'Lesson' }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $quiz->getTranslation($locale, 'title') ?? $quiz->title ?? 'Quiz' }}
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Lesson') }}
                </a>
            </div>

            {{-- Quiz Header --}}
            <div class="card quiz-info-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ $quiz->getTranslation($locale, 'title') ?? $quiz->title ?? 'Quiz' }}
                    </h1>
                </div>
                <div class="card-body">
                    @php
                        $quizDescription = $quiz->getTranslation($locale, 'description');
                    @endphp

                    @if($quizDescription)
                        <p class="lead quiz-description">{{ $quizDescription }}</p>
                    @endif

                    <div class="quiz-meta">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="meta-item">
                                    <i class="fas fa-clock meta-icon"></i>
                                    <div class="meta-content">
                                        <strong>{{ __('Time Limit') }}</strong>
                                        <span>{{ $quiz->time_limit ? $quiz->time_limit . ' ' . __('minutes') : __('No limit') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="meta-item">
                                    <i class="fas fa-trophy meta-icon"></i>
                                    <div class="meta-content">
                                        <strong>{{ __('Passing Score') }}</strong>
                                        <span>{{ $quiz->passing_score }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="meta-item">
                                    <i class="fas fa-redo meta-icon"></i>
                                    <div class="meta-content">
                                        <strong>{{ __('Attempts') }}</strong>
                                        <span>{{ $attemptsCount }}/{{ $quiz->max_attempts > 0 ? $quiz->max_attempts : '∞' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$canAttempt)
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('You have reached the maximum number of attempts for this quiz.') }}
                        </div>
                    @endif
                </div>
            </div>

            @if($canAttempt)
                {{-- Start Quiz Form --}}
                <form action="{{ route('frontend.quizzes.start', [
                    'locale' => $locale,
                    'course' => $course->id,
                    'lesson' => $lesson->id
                ]) }}" method="POST" id="start-quiz-form">
                    @csrf
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg start-quiz-btn">
                            <i class="fas fa-play me-2"></i> {{ __('Start Quiz') }}
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
                        'attempt' => $quiz->attempts()->where('student_id', $student->id)->latest()->first()->id ?? 0
                    ]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-2"></i> {{ __('View Previous Results') }}
                    </a>
                </div>
            @endif

            {{-- Previous Attempts --}}
            @if($quiz->attempts->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> {{ __('Previous Attempts') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="attempts-list">
                            @foreach($quiz->attempts->sortByDesc('created_at') as $attempt)
                                <div class="attempt-item">
                                    <div class="attempt-date">{{ $attempt->created_at->format('d.m.Y H:i') }}</div>
                                    <div class="attempt-score {{ $attempt->score >= $quiz->passing_score ? 'text-success' : 'text-danger' }}">
                                        {{ $attempt->score }}%
                                    </div>
                                    <a href="{{ route('frontend.quizzes.results', [
                                        'locale' => $locale,
                                        'course' => $course->id,
                                        'lesson' => $lesson->id,
                                        'attempt' => $attempt->id
                                    ]) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.quiz-info-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
}

.quiz-description {
    color: #6c757d;
    font-size: 1.1em;
    line-height: 1.6;
}

.quiz-meta {
    margin-top: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
}

.meta-icon {
    font-size: 1.5em;
    color: #007bff;
    margin-right: 1rem;
    width: 40px;
    text-align: center;
}

.meta-content {
    display: flex;
    flex-direction: column;
}

.meta-content strong {
    font-size: 0.9em;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.meta-content span {
    font-size: 1.1em;
    font-weight: 600;
    color: #343a40;
}

.start-quiz-btn {
    padding: 1rem 2.5rem;
    font-size: 1.2em;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
    border: none;
}

.start-quiz-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    background-color: #0056b3;
}

.attempts-list {
    max-height: 300px;
    overflow-y: auto;
}

.attempt-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.attempt-item:hover {
    background-color: #f8f9fa;
    border-radius: 6px;
}

.attempt-item:last-child {
    border-bottom: none;
}

.attempt-date {
    color: #6c757d;
    font-size: 0.9em;
}

.attempt-score {
    font-weight: 600;
    font-size: 1.1em;
}

@media (max-width: 768px) {
    .meta-item {
        margin-bottom: 1rem;
    }

    .attempt-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .attempt-item .btn {
        align-self: flex-end;
    }
}
</style>
@endsection
