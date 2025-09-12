{{-- resources/views/frontend/lessons/show.blade.php --}}
@extends('frontend.layouts.app')

@section('title', __('Lesson: :title', ['title' => $lesson->title]))

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1>{{ $lesson->title }}</h1>

            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.watchCourse', ['locale' => $locale, 'id' => $course->id]) }}">
                            {{ $course->title }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">{{ $lesson->title }}</li>
                </ol>
            </nav>

            {{-- Контент урока --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-play-circle me-2"></i>
                        Содержание урока
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
                                        Ваш браузер не поддерживает видео
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
                                <small class="text-muted">Просмотрено: {{ $currentProgress }}%</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-video-slash me-2"></i>
                            Видео не доступно
                        </div>
                    @endif

                    {{-- Описание урока --}}
                    @if($lesson->description)
                    <div class="mb-4">
                        <h5>Описание урока:</h5>
                        <p class="text-muted">{{ $lesson->description }}</p>
                    </div>
                    @endif

                    {{-- Заметки к уроку --}}
                    @if($lesson->notes)
                    <div class="mt-4">
                        <h5>
                            <i class="fas fa-sticky-note me-2"></i>
                            Заметки к уроку:
                        </h5>
                        <div class="bg-light p-4 rounded">
                            {!! nl2br(e($lesson->notes)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Материалы урока --}}
                    @if($lesson->materials && $lesson->materials->count() > 0)
                    <div class="mt-4">
                        <h5>
                            <i class="fas fa-file-download me-2"></i>
                            Материалы для скачивания:
                        </h5>
                        <div class="list-group">
                            @foreach($lesson->materials as $material)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($material->type === 'document')
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                        @elseif($material->type === 'presentation')
                                            <i class="fas fa-file-powerpoint text-warning me-2"></i>
                                        @else
                                            <i class="fas fa-file text-secondary me-2"></i>
                                        @endif
                                        <strong>{{ $material->title }}</strong>
                                        <small class="text-muted ms-2">({{ $material->type }})</small>
                                    </div>
                                    <a href="{{ $material->file_path }}"
                                       class="btn btn-outline-primary btn-sm"
                                       download>
                                        <i class="fas fa-download me-1"></i> Скачать
                                    </a>
                                </div>
                                @if($material->description)
                                <p class="mt-2 mb-0 small text-muted">{{ $material->description }}</p>
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
                        <a href="{{ route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $previousLesson->id]) }}"
                           class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Предыдущий урок
                        </a>
                        @else
                        <span></span>
                        @endif

                        @if($nextLesson)
                        <a href="{{ route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $nextLesson->id]) }}"
                           class="btn btn-primary">
                            Следующий урок <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        @else
                       <a href="{{ route('frontend.watchCourse', ['locale' => $locale, 'id' => $course->id]) }}"
                           class="btn btn-success">
                            <i class="fas fa-check me-2"></i> Завершить курс
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Квиз (если есть) --}}
            @if($lesson->quiz)
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Тест к уроку
                    </h4>
                </div>
                <div class="card-body">
                    <h5>{{ $lesson->quiz->title }}</h5>
                    <p class="text-muted">{{ $lesson->quiz->description }}</p>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="fas fa-clock me-2"></i> Время:</strong>
                            {{ $lesson->quiz->time_limit ? $lesson->quiz->time_limit . ' мин' : 'Без ограничения' }}
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-trophy me-2"></i> Проходной балл:</strong> {{ $lesson->quiz->passing_score }}%
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-redo me-2"></i> Попытки:</strong> {{ $lesson->quiz->max_attempts }}
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('frontend.quizzes.show', [
    'locale' => $locale,
    'course' => $course->id,
    'lesson' => $lesson->id,
    'quiz' => $lesson->quiz->id
]) }}" class="btn btn-primary">
                            <i class="fas fa-play me-2"></i> Начать тест
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
// Скрипт для отслеживания прогресса просмотра видео
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('lesson-video');

    // Восстановление позиции просмотра
    @if($currentProgress > 0)
    video.currentTime = (video.duration * {{ $currentProgress }}) / 100;
    @endif

    // Отслеживание прогресса
    video.addEventListener('timeupdate', function() {
        if (video.duration > 0) {
            const progress = (video.currentTime / video.duration) * 100;

            // Обновляем прогресс каждые 5 секунд
            if (Math.round(progress) % 5 === 0) {
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
                });
            }
        }
    });
});
</script>
@endif
@endsection
