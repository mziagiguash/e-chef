@extends('frontend.layouts.app')

@section('title', __('Quiz Results'))

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url("/$locale") }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('frontend.courses.show', [$locale, $course->id]) }}">{{ $course->translations->firstWhere('locale', $locale)->title ?? $course->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lessons.show', [$locale, $course->id, $lesson->id]) }}">{{ $lesson->translations->firstWhere('locale', $locale)->title ?? $lesson->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('frontend.quizzes.show', [$locale, $course->id, $lesson->id, $quiz->id]) }}">{{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->title }}</a></li>
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
                    @foreach($attempt->answers as $answer)
                        @php
                            $question = $answer->question;
                            $questionTranslation = $question->translations->firstWhere('locale', $locale);
                            $questionText = $questionTranslation->content ?? $question->question_text;
                        @endphp

                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">{{ $questionText }}</h6>
                                <p class="text-{{ $answer->is_correct ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $answer->is_correct ? 'check' : 'times' }} me-2"></i>
                                    {{ $answer->is_correct ? __('Correct') : __('Incorrect') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
