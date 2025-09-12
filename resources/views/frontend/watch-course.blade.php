@extends('frontend.layouts.app')

@section('title', $currentTitle)

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home', ['locale' => $locale]) }}">{{ __('Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $currentTitle }}</li>
                </ol>
            </nav>

            {{-- Заголовок курса --}}
            <div class="text-center mb-5">
                <h1 class="display-4">{{ $currentTitle }}</h1>
                <p class="lead text-muted">{{ $course->course_code }}</p>
            </div>

            {{-- Основная информация о курсе --}}
            <div class="row mb-5">
                <div class="col-lg-8">
                    {{-- Видео превью --}}
                    @if($course->thumbnail_video_url)
                    <div class="card mb-4">
                        <div class="card-body p-0">
                            <div class="ratio ratio-16x9">
                                @if(str_contains($course->thumbnail_video_url, 'youtube.com') || str_contains($course->thumbnail_video_url, 'youtu.be'))
                                    <iframe src="https://www.youtube.com/embed/{{ extractYouTubeId($course->thumbnail_video_url) }}"
                                            frameborder="0"
                                            allowfullscreen>
                                    </iframe>
                                @else
                                    <video controls poster="{{ $course->thumbnail_image ?? '' }}">
                                        <source src="{{ $course->thumbnail_video_url }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Описание курса --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Описание курса</h4>
                        </div>
                        <div class="card-body">
                            <p>{{ $currentDescription }}</p>
                        </div>
                    </div>

{{-- Что вы узнаете --}}
@if($currentPrerequisites && $currentPrerequisites != 'No Prerequisites')
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Чему вы научитесь</h4>
    </div>
    <div class="card-body">
        {!! nl2br(e($currentPrerequisites)) !!}
    </div>
</div>
@endif
                </div>

                <div class="col-lg-4">
                    {{-- Боковая панель с информацией --}}
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0"><i class="fas fa-cogs me-2"></i>Информация о курсе</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong><i class="fas fa-user-tie me-2"></i>Инструктор:</strong>
                                <br>
                                @if($course->instructor)
                                    {{ $course->instructor->name }}
                                @else
                                    <span class="text-muted">Не указан</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-layer-group me-2"></i>Категория:</strong>
                                <br>
                                {{ $course->courseCategory->name ?? 'Не указана' }}
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-play-circle me-2"></i>Тип курса:</strong>
                                <br>
                                @if($course->courseType === 'free')
                                    <span class="badge bg-success">Бесплатный</span>
                                @elseif($course->courseType === 'paid')
                                    <span class="badge bg-warning">Платный</span>
                                @else
                                    <span class="badge bg-info">По подписке</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-credit-card me-2"></i>Цена:</strong>
                                <br>
                                @if($course->courseType === 'free')
                                    <span class="text-success fw-bold">Бесплатно</span>
                                @else
                                    <span class="text-primary fw-bold">{{ number_format($course->coursePrice, 2) }} ₽</span>
                                    @if($course->courseOldPrice && $course->courseOldPrice > 0)
                                        <br>
                                        <small class="text-muted text-decoration-line-through">
                                            {{ number_format($course->courseOldPrice, 2) }} ₽
                                        </small>
                                    @endif
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-calendar me-2"></i>Начало:</strong>
                                <br>
                                {{ $course->start_from ? \Carbon\Carbon::parse($course->start_from)->format('d.m.Y') : 'Не указано' }}
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-clock me-2"></i>Длительность:</strong>
                                <br>
                                {{ $course->duration ?? 0 }} часов
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-book me-2"></i>Уроков:</strong>
                                <br>
                                {{ $totalLessons }} уроков
                            </div>

                            @if($course->tag)
                            <div class="mb-3">
                                <strong><i class="fas fa-tag me-2"></i>Статус:</strong>
                                <br>
                                @if($course->tag === 'popular')
                                    <span class="badge bg-danger">Популярный</span>
                                @elseif($course->tag === 'featured')
                                    <span class="badge bg-info">Рекомендуемый</span>
                                @else
                                    <span class="badge bg-warning">Скоро</span>
                                @endif
                            </div>
                            @endif

                            @if($currentKeywords)
                            <div class="mb-3">
                                <strong><i class="fas fa-hashtag me-2"></i>Ключевые слова:</strong>
                                <br>
                                <div class="mt-1">
                                    @foreach(explode(',', $currentKeywords) as $keyword)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ trim($keyword) }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Прогресс --}}
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5>Прогресс курса</h5>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     style="width: {{ $progress }}%">
                                    {{ $progress }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $completedLessons }}/{{ $totalLessons }} уроков завершено
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Список уроков --}}
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Программа курса ({{ $totalLessons }} уроков)
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($course->lessons as $index => $lesson)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            {{ $lesson->title }}
                                        </h5>

                                        @if($lesson->description)
                                        <p class="mb-2 text-muted">{{ Str::limit($lesson->description, 150) }}</p>
                                        @endif

                                        {{-- Информация о уроке --}}
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($lesson->video_url)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-video me-1"></i> Видео
                                                </span>
                                            @endif

                                            @if($lesson->quiz)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-graduation-cap me-1"></i> Тест
                                                </span>
                                            @endif

                                            @if($lesson->materials && $lesson->materials->count() > 0)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-file-download me-1"></i> Материалы ({{ $lesson->materials->count() }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0">
                                        <a href="{{ route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $lesson->id]) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-play me-1"></i> Начать
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <h5>Уроки пока не добавлены</h5>
                                <p class="text-muted">Следите за обновлениями курса</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
