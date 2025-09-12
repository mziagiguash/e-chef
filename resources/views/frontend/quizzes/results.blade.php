@extends('frontend.layouts.app')

@section('title', __('Quiz Results'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Results Card --}}
            <div class="card">
                <div class="card-header {{ $passed ? 'bg-success' : 'bg-danger' }} text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-{{ $passed ? 'check-circle' : 'times-circle' }} me-2"></i>
                        {{ $passed ? __('Passed') : __('Failed') }}
                    </h1>
                </div>
                <div class="card-body">
                    {{-- Quiz Info --}}
                    <div class="text-center mb-4">
                        <h2>{{ $quiz->translations->firstWhere('locale', $locale)->title ?? $quiz->title ?? 'Quiz' }}</h2>
                        @php
                            $quizDescription = $quiz->translations->firstWhere('locale', $locale)->description ?? null;
                        @endphp
                        @if($quizDescription)
                            <p class="text-muted">{{ $quizDescription }}</p>
                        @endif
                    </div>

                    {{-- Score --}}
                    <div class="text-center mb-4">
                        <div class="display-1 {{ $passed ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $attempt->score }}%
                        </div>
                        <p class="lead">
                            {{ $attempt->correct_answers }} {{ __('out of') }} {{ $attempt->total_questions }} {{ __('questions correct') }}
                        </p>
                        <p class="text-muted">
                            {{ __('Passing score') }}: {{ $quiz->passing_score }}%
                        </p>

                        @if($attempt->time_taken)
                            <p class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ __('Time taken') }}: {{ $attempt->time_taken_formatted }}
                            </p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="text-center">
                        @if($passed)
                            <div class="alert alert-success">
                                <i class="fas fa-trophy me-2"></i>
                                {{ __('Congratulations! You passed the quiz.') }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('Keep studying! You can try again.') }}
                            </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('lessons.show', [
                                'locale' => $locale,
                                'course' => $course->id,
                                'lesson' => $lesson->id
                            ]) }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Lesson') }}
                            </a>

                            @if(!$passed && ($quiz->max_attempts === 0 || $quiz->attempts()->where('user_id', auth()->id())->count() < $quiz->max_attempts))
                                <a href="{{ route('frontend.quizzes.show', [
                                    'locale' => $locale,
                                    'course' => $course->id,
                                    'lesson' => $lesson->id,
                                    'quiz' => $quiz->id
                                ]) }}" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i> {{ __('Try Again') }}
                                </a>
                            @endif
                        </div>

                        {{-- Next Lesson --}}
                        @if($passed && $course && $lesson)
                            @php
                                $nextLesson = \App\Models\Lesson::where('course_id', $course->id)
                                    ->where('id', '>', $lesson->id)
                                    ->orderBy('id')
                                    ->first();
                            @endphp

                            @if($nextLesson)
                                <div class="mt-4">
                                    <a href="{{ route('lessons.show', [
                                        'locale' => $locale,
                                        'course' => $course->id,
                                        'lesson' => $nextLesson->id
                                    ]) }}" class="btn btn-success">
                                        <i class="fas fa-arrow-right me-2"></i> {{ __('Continue to Next Lesson') }}
                                    </a>
                                    <p class="text-muted mt-2">
                                        {{ __('Next') }}: {{ $nextLesson->translations->firstWhere('locale', $locale)->title ?? $nextLesson->title ?? 'Next Lesson' }}
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    {{ __("Congratulations! You've completed this course.") }}
                                    <a href="{{ route('frontend.watchCourse', [
                                        'locale' => $locale,
                                        'id' => $course->id
                                    ]) }}" class="btn btn-outline-info btn-sm ms-2">
                                        {{ __('View Course') }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
