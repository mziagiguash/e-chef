{{-- resources/views/frontend/lessons/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title', __('Lesson: :title', ['title' => $lessonTranslation->title ?? $lesson->title]))

@section('content')

<div class="container py-5">
    <div class="row">

{{-- Боковая панель с содержанием курса --}}
<div class="col-md-4 col-lg-3 order-2 order-md-1">
    <div class="card sticky-top" style="top: 100px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list-ol me-2"></i>
                {{ __('Course Content') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($courseLessons as $courseLesson)
                    @php
                        $courseLessonTranslation = $courseLesson->translations->where('locale', $locale)->first()
                            ?? $courseLesson->translations->where('locale', 'en')->first();
                        $isCurrent = $courseLesson->id === $lesson->id;
                        $lessonProgress = $userLessonProgress[$courseLesson->id] ?? ['progress' => 0, 'is_completed' => false, 'is_available' => true];
                        $isCompleted = $lessonProgress['is_completed'];
                        $isAvailable = $lessonProgress['is_available'];
                        $progressValue = $lessonProgress['progress'];
                    @endphp

                    <a href="{{ $isAvailable ? route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $courseLesson->id]) : '#' }}"
                       class="list-group-item list-group-item-action border-0
                              {{ $isCurrent ? 'active' : '' }}
                              {{ $isCompleted ? 'list-group-item-success' : '' }}
                              {{ !$isAvailable ? 'disabled text-muted' : '' }}"
                       {{ !$isAvailable ? 'onclick="return false;" style="cursor: not-allowed;"' : '' }}>
                        <div class="d-flex align-items-center">
                            {{-- Иконка статуса --}}
                            @if($isCompleted)
                                <span class="text-success me-2">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @elseif($isCurrent)
                                <span class="me-2">
                                    <i class="fas fa-play-circle"></i>
                                </span>
                            @elseif(!$isAvailable)
                                <span class="text-muted me-2">
                                    <i class="fas fa-lock"></i>
                                </span>
                            @else
                                <span class="text-muted me-2">
                                    <i class="far fa-circle"></i>
                                </span>
                            @endif

                            {{-- Название урока --}}
                            <div class="flex-grow-1">
                                <small class="d-block {{ $isCurrent ? 'text-white' : 'text-muted' }}">
                                    {{ __('Lesson :number', ['number' => $courseLesson->order]) }}
                                </small>
                                <span class="fw-medium {{ !$isAvailable ? 'text-muted' : '' }}">
                                    {{ $courseLessonTranslation->title ?? $courseLesson->title }}
                                </span>
                            </div>

                            {{-- Прогресс урока --}}
                            @if($progressValue > 0 && !$isCompleted && $isAvailable)
                                <small class="{{ $isCurrent ? 'text-white' : 'text-muted' }} ms-2">
                                    {{ $progressValue }}%
                                </small>
                            @endif

                            {{-- Длительность --}}
                            @if($courseLesson->duration && $isAvailable)
                                <small class="{{ $isCurrent ? 'text-white' : 'text-muted' }} ms-2">
                                    {{ gmdate('i:s', $courseLesson->duration) }}
                                </small>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="progress mb-2">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $progressPercentage }}%;"
                     aria-valuenow="{{ $progressPercentage }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted">
                {{ __('Progress: :completed/:total lessons (:percent%)', [
                    'completed' => $completedLessonsCount,
                    'total' => $totalLessons,
                    'percent' => $progressPercentage
                ]) }}
            </small>
        </div>
    </div>
</div>

        {{-- Основной контент урока --}}
        <div class="col-md-8 col-lg-9 order-1 order-md-2">
            <h1>{{ $lessonTranslation->title ?? $lesson->title }}</h1>

            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', ['locale' => $locale]) }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>{{ __('Home') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.courses', ['locale' => $locale]) }}" class="text-decoration-none">
                            {{ __('Courses') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]) }}">
                            {{ $courseTranslation->title ?? $course->title }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">{{ $lessonTranslation->title ?? $lesson->title }}</li>
                </ol>
            </nav>

            {{-- Контент урока --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-play-circle me-2"></i>
                        {{ __('Lesson Content') }}
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Видеоплеер --}}
                    @if($lesson->video_url)
                        <div class="video-container mb-4">
                            @if($isYouTube)
                                {{-- YouTube видео --}}
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/{{ $youTubeId }}?rel=0"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                    </iframe>
                                </div>
                            @else
                                {{-- Загруженное видео --}}
                                <div class="ratio ratio-16x9">
                                    <video controls class="w-100" id="lesson-video">
                                        <source src="{{ $lesson->video_url }}" type="video/mp4">
                                        {{ __('Your browser does not support the video tag.') }}
                                    </video>
                                </div>

                                {{-- Прогресс просмотра (только для загруженных видео) --}}
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $currentProgress }}%;"
                                         aria-valuenow="{{ $currentProgress }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ __('Watched: :progress%', ['progress' => $currentProgress]) }}</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-video-slash me-2"></i>
                            {{ __('Video not available') }}
                        </div>
                    @endif

                    {{-- Описание урока --}}
                    @if($lessonTranslation->description ?? $lesson->description)
                    <div class="mb-4">
                        <h5>{{ __('Lesson Description:') }}</h5>
                        <p class="text-muted">{{ $lessonTranslation->description ?? $lesson->description }}</p>
                    </div>
                    @endif

                    {{-- Заметки к уроку --}}
                    @if($lessonTranslation->notes ?? $lesson->notes)
                    <div class="mt-4">
                        <h5>
                            <i class="fas fa-sticky-note me-2"></i>
                            {{ __('Lesson Notes:') }}
                        </h5>
                        <div class="bg-light p-4 rounded">
                            {!! nl2br(e($lessonTranslation->notes ?? $lesson->notes)) !!}
                        </div>
                    </div>
                    @endif

{{-- Материалы урока --}}
@php
    $materials = $lesson->materials ?? collect();
    // Если materials - это строка, преобразуем в коллекцию
    if (is_string($materials)) {
        $materials = collect();
    }
    $materialsCount = is_object($materials) && method_exists($materials, 'count') ? $materials->count() : 0;
@endphp

@if($materialsCount > 0)
<div class="mt-4">
    <h5>
        <i class="fas fa-file-download me-2"></i>
        {{ __('Downloadable Materials:') }}
    </h5>
    <div class="list-group">
        @foreach($materials as $material)
            @php
                $materialTranslation = $material->translations->where('locale', $locale)->first()
                    ?? $material->translations->where('locale', 'en')->first();
            @endphp
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($material->type === 'document')
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                        @elseif($material->type === 'presentation')
                            <i class="fas fa-file-powerpoint text-warning me-2"></i>
                        @elseif($material->type === 'audio') {{-- 🔴 ДОБАВЛЕНО audio --}}
                            <i class="fas fa-music text-info me-2"></i>
                        @else
                            <i class="fas fa-file text-secondary me-2"></i>
                        @endif
                        <strong>{{ $materialTranslation->title ?? $material->title }}</strong>
                        <small class="text-muted ms-2">({{ $material->type }})</small>
                    </div>
                    <a href="{{ $material->file_path }}"
                       class="btn btn-outline-primary btn-sm"
                       download>
                        <i class="fas fa-download me-1"></i> {{ __('Download') }}
                    </a>
                </div>
                @if($materialTranslation->description ?? $material->description)
                <p class="mt-2 mb-0 small text-muted">{{ $materialTranslation->description ?? $material->description }}</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
                </div>
            </div>

            {{-- Навигация между уроками --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        @if($previousLesson)
                        @php
                            $prevLessonTranslation = $previousLesson->translations->where('locale', $locale)->first()
                                ?? $previousLesson->translations->where('locale', 'en')->first();
                        @endphp
                        <a href="{{ route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $previousLesson->id]) }}"
                           class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> {{ __('Previous Lesson') }}
                        </a>
                        @else
                        <span></span>
                        @endif

                        @if($nextLesson)
                        @php
                            $nextLessonTranslation = $nextLesson->translations->where('locale', $locale)->first()
                                ?? $nextLesson->translations->where('locale', 'en')->first();
                        @endphp
                        <a href="{{ route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $nextLesson->id]) }}"
                           class="btn btn-primary">
                            {{ __('Next Lesson') }} <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        @else
                        <a href="{{ route('frontend.courses.show', ['locale' => $locale, 'course' => $course->id]) }}"
                           class="btn btn-success">
                            <i class="fas fa-check me-2"></i> {{ __('Complete Course') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Квиз (если есть) --}}
            @if($lesson->quiz)
            @php
                $quizTranslation = $lesson->quiz->translations->where('locale', $locale)->first()
                    ?? $lesson->quiz->translations->where('locale', 'en')->first();
            @endphp
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ __('Lesson Quiz') }}
                    </h4>
                </div>
                <div class="card-body">
                    <h5>{{ $quizTranslation->title ?? $lesson->quiz->title }}</h5>
                    <p class="text-muted">{{ $quizTranslation->description ?? $lesson->quiz->description }}</p>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="fas fa-clock me-2"></i> {{ __('Time:') }}</strong>
                            {{ $lesson->quiz->time_limit ? $lesson->quiz->time_limit . ' ' . __('min') : __('No time limit') }}
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-trophy me-2"></i> {{ __('Passing Score:') }}</strong> {{ $lesson->quiz->passing_score }}%
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-redo me-2"></i> {{ __('Attempts:') }}</strong> {{ $lesson->quiz->max_attempts }}
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('frontend.quizzes.show', [
                            'locale' => $locale,
                            'course' => $course->id,
                            'lesson' => $lesson->id
                        ]) }}" class="btn btn-primary">
                            <i class="fas fa-play me-2"></i> {{ __('Start Quiz') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
@if($lesson->video_url && !$isYouTube)
<script>
// В шаблоне, в секции scripts
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('lesson-video');

    if (!video) return;

    // Восстановление позиции просмотра
    @if($currentProgress > 0)
    video.addEventListener('loadedmetadata', function() {
        video.currentTime = (video.duration * {{ $currentProgress }}) / 100;
    });
    @endif

    // Отслеживание прогресса
    video.addEventListener('timeupdate', function() {
        if (video.duration > 0) {
            const progress = (video.currentTime / video.duration) * 100;

            // Обновляем прогресс каждые 10 секунд или при значительном изменении
            if (Math.round(progress) % 10 === 0 || progress >= 90) {
                fetch('{{ route("lessons.progress.update", ["locale" => $locale, "lesson" => $lesson->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        progress: Math.round(progress),
                        video_position: Math.floor(video.currentTime),
                        video_duration: Math.floor(video.duration)
                    })
                }).then(response => response.json())
                .then(data => {
                    console.log('Progress updated:', data);
                    // Можно обновить прогресс в реальном времени на странице
                });
            }
        }
    });

    // Обновляем прогресс при завершении видео
    video.addEventListener('ended', function() {
        fetch('{{ route("lessons.progress.update", ["locale" => $locale, "lesson" => $lesson->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                progress: 100,
                video_position: Math.floor(video.duration),
                video_duration: Math.floor(video.duration)
            })
        });
    });
});
</script>
@endif
@endsection
