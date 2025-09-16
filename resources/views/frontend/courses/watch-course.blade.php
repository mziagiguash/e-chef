@extends('frontend.layouts.app')

@section('title', $currentTitle)

@section('content')

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-3">
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
                    <li class="breadcrumb-item active text-truncate" style="max-width: 200px;" title="{{ $currentTitle }}">
                        {{ $currentTitle }}
                    </li>
                </ol>
            </nav>

            {{-- Заголовок курса --}}
            <div class="text-center mb-4">
                <h1 class="h2 fw-bold mb-2">{{ $currentTitle }}</h1>
                @if($course->course_code)
                <p class="text-muted mb-0">{{ $course->course_code }}</p>
                @endif
            </div>

            {{-- Основная информация о курсе --}}
            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Видео превью --}}
                    @if($course->thumbnail_video_url || $course->thumbnail_image)
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="ratio ratio-16x9">
                                @if($course->thumbnail_video_url)
                                    @if(str_contains($course->thumbnail_video_url, 'youtube.com') || str_contains($course->thumbnail_video_url, 'youtu.be'))
                                        @php
                                            $videoId = null;
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $course->thumbnail_video_url, $matches)) {
                                                $videoId = $matches[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}?rel=0"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen
                                                    loading="lazy">
                                            </iframe>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-dark text-white h-100">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                {{ __('Invalid video URL') }}
                                            </div>
                                        @endif
                                    @else
                                        <video controls poster="{{ $course->thumbnail_image ?? '' }}" class="w-100 h-100" style="object-fit: cover;">
                                            <source src="{{ $course->thumbnail_video_url }}" type="video/mp4">
                                            {{ __('Your browser does not support the video tag.') }}
                                        </video>
                                    @endif
                                @elseif($course->thumbnail_image)
                                    <img src="{{ $course->thumbnail_image }}" alt="{{ $currentTitle }}" class="w-100 h-100" style="object-fit: cover;">
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Описание курса --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('Course Description') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $currentDescription }}</p>
                        </div>
                    </div>

                    {{-- Что вы узнаете --}}
                    @if($currentPrerequisites && $currentPrerequisites != 'No Prerequisites')
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-secondary text-black">
                            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>{{ __('What You Will Learn') }}</h5>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($currentPrerequisites)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Список уроков --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                {{ __('Course Curriculum') }} ({{ $totalLessons }} {{ __('lessons') }})
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($course->lessons as $index => $lesson)
                                    @php
                                        $lessonTranslation = $lesson->translations->where('locale', $locale)->first();
                                        $lessonTitle = $lessonTranslation->title ?? $lesson->translations->first()->title ?? $lesson->title ?? __('No Title');
                                        $lessonDescription = $lessonTranslation->description ?? $lesson->translations->first()->description ?? $lesson->description ?? '';

                                        // Получаем переводы для материалов и квизов
                                        $materialsCount = $lesson->materials ? $lesson->materials->count() : 0;
                                        $hasQuiz = $lesson->quiz ? true : false;

                                        // Определяем доступность урока (первый урок всегда доступен)
                                        $isAvailable = $index === 0 ? true : ($lesson->is_available ?? false);
                                    @endphp
                                    <div class="list-group-item border-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
        <div class="flex-grow-1" style="min-width: 0;">
            <div class="d-flex align-items-start mb-2">
                <span class="badge bg-primary me-2 mt-1">{{ $index + 1 }}</span>
                <div class="flex-grow-1" style="min-width: 0;">
                    <h6 class="mb-1 text-truncate" title="{{ $lessonTitle }}">{{ $lessonTitle }}</h6>

                    @if($lessonDescription)
                    <div class="lesson-description mb-2">
                        <p class="text-muted small mb-1 description-preview" style="display: block;">
                            {{ Str::limit($lessonDescription, 120) }}
                        </p>
                        @if(strlen($lessonDescription) > 120)
                        <button class="btn btn-link p-0 text-primary toggle-description"
                                data-full="{{ e($lessonDescription) }}"
                                data-preview="{{ Str::limit($lessonDescription, 120) }}">
                            <small>{{ __('Show more') }}</small>
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Информация о уроке --}}
            <div class="d-flex flex-wrap gap-2 mb-2">
                @if($lesson->video_url)
                    @php
                        $colors = ['#4681f4', '#5783db', '#55c2da', '#5dbea3', '#33b249'];
                        $color = $colors[array_rand($colors)];
                    @endphp
                    <span class="keyword-badge" style="background-color: {{ $color }}">
                        <i class="fas fa-video me-1"></i> {{ __('Video') }}
                    </span>
                @endif

                @if($hasQuiz)
                    @php
                        $colors = ['#ffc107', '#ffb300', '#ffa000', '#ff8f00', '#ff6f00'];
                        $color = $colors[array_rand($colors)];
                    @endphp
                    <span class="keyword-badge" style="background-color: {{ $color }}; color: #000;">
                        <i class="fas fa-graduation-cap me-1"></i> {{ __('Quiz') }}
                    </span>
                @endif

                @if($materialsCount > 0)
                    @php
                        $colors = ['#28a745', '#259b3f', '#218838', '#1e7e34', '#1c7430'];
                        $color = $colors[array_rand($colors)];
                    @endphp
                    <span class="keyword-badge" style="background-color: {{ $color }}">
                        <i class="fas fa-file-download me-1"></i> {{ __('Materials') }} ({{ $materialsCount }})
                    </span>
                @endif
            </div>
        </div>

        <div class="flex-shrink-0">
            {{-- Проверяем доступность урока --}}
            @if($isAvailable)
                <a href="{{ route('lessons.show', [
                    'locale' => $locale,
                    'course' => $course->id,
                    'lesson' => $lesson->id
                ]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-play me-1"></i> {{ __('Start') }}
                </a>
            @else
                <button class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-lock me-1"></i> {{ __('Locked') }}
                </button>
            @endif
        </div>
    </div>
</div>
                                @empty
                                    <div class="list-group-item text-center py-5">
                                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                        <h6 class="mb-2">{{ __('No lessons added yet') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('Follow course updates') }}</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Пагинация --}}
                            @if($course->lessons->count() > 10)
                            <div class="card-footer bg-light">
                                <nav aria-label="Lesson navigation">
                                    <ul class="pagination pagination-sm justify-content-center mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">{{ __('Previous') }}</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">{{ __('Next') }}</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Боковая панель с информацией --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('Course Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong><i class="fas fa-user-tie me-2 text-primary"></i>{{ __('Instructor') }}:</strong>
                                <br>
                                <span class="ms-4">{{ $instructorName }}</span>
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-layer-group me-2 text-primary"></i>{{ __('Category') }}:</strong>
                                <br>
                                <span class="ms-4">{{ $categoryName }}</span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-play-circle me-2 text-primary"></i>{{ __('Course Type') }}:</strong>
                                <br>
                                <span class="ms-4">
                                    @php
                                        $colors = ['#4681f4', '#5783db', '#55c2da', '#5dbea3', '#33b249', '#5adbb5', '#a881af', '#80669d', '#dd7973', '#ffbd03', '#ED0800'];
                                        $color = $colors[array_rand($colors)];
                                    @endphp

                                    @if($course->course_type === 'free')
                                        <span class="random-badge" style="background-color: {{ $color }};">
                                            {{ __('Free') }}
                                        </span>
                                    @elseif($course->course_type === 'paid')
                                        <span class="random-badge" style="background-color: {{ $color }};">
                                            {{ __('Paid') }}
                                        </span>
                                    @else
                                        <span class="random-badge" style="background-color: {{ $color }};">
                                            {{ __('Subscription') }}
                                        </span>
                                    @endif
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-credit-card me-2 text-primary"></i>{{ __('Price') }}:</strong>
                                <br>
                                <span class="ms-4">
                                    @if($course->course_type === 'free')
                                        <span class="text-success fw-bold">{{ __('Free') }}</span>
                                    @else
                                        <span class="text-primary fw-bold">{{ number_format($course->price, 0) }} {{ $currencySymbol }}</span>
                                        @if($course->old_price && $course->old_price > 0)
                                            <br>
                                            <small class="text-muted text-decoration-line-through">
                                                {{ number_format($course->old_price, 0) }} {{ $currencySymbol }}
                                            </small>
                                        @endif
                                    @endif
                                </span>
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-calendar me-2 text-primary"></i>{{ __('Start Date') }}:</strong>
                                <br>
                                <span class="ms-4">{{ $course->start_from ? \Carbon\Carbon::parse($course->start_from)->format('d.m.Y') : __('Not specified') }}</span>
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-clock me-2 text-primary"></i>{{ __('Duration') }}:</strong>
                                <br>
                                <span class="ms-4">{{ $course->duration ?? 0 }} {{ __('hours') }}</span>
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-book me-2 text-primary"></i>{{ __('Lessons') }}:</strong>
                                <br>
                                <span class="ms-4">{{ $totalLessons }} {{ __('lessons') }}</span>
                            </div>

                            @if($course->tag)
                            <div class="mb-3">
                                <strong><i class="fas fa-tag me-2 text-primary"></i>{{ __('Status') }}:</strong>
                                <br>
                                <span class="ms-4">
                                    @if($course->tag === 'popular')
                                        {{ __('Popular') }}
                                    @elseif($course->tag === 'featured')
                                        {{ __('Featured') }}
                                    @else
                                        {{ __('Coming Soon') }}
                                    @endif
                                </span>
                            </div>
                            @endif

                            @if($currentKeywords)
                            <div class="mb-3">
                                <strong><i class="fas fa-hashtag me-2 text-primary"></i>{{ __('Keywords') }}:</strong>
                                <br>
                                <div class="ms-4 mt-1 keyword-container">
                                    @foreach(array_filter(explode(',', $currentKeywords)) as $keyword)
                                        @php
                                            $colors = ['#4681f4', '#5783db', '#55c2da', '#5dbea3', '#33b249', '#5adbb5', '#a881af', '#80669d', '#dd7973', '#ffbd03', '#ED0800'];
                                            $color = $colors[array_rand($colors)];
                                        @endphp
                                        <span class="keyword-badge" style="background-color: {{ $color }}">{{ trim($keyword) }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Прогресс --}}
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="mb-3">{{ __('Course Progress') }}</h6>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     style="width: {{ $progress }}%">
                                    {{ $progress }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $completedLessons }}/{{ $totalLessons }} {{ __('lessons completed') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция для переключения описания уроков
    const toggleButtons = document.querySelectorAll('.toggle-description');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const descriptionDiv = this.closest('.lesson-description');
            const preview = descriptionDiv.querySelector('.description-preview');
            const fullText = this.getAttribute('data-full');
            const previewText = this.getAttribute('data-preview');

            if (this.textContent.trim() === '{{ __("Show more") }}') {
                preview.textContent = fullText;
                this.innerHTML = '<small>{{ __("Show less") }}</small>';
            } else {
                preview.textContent = previewText;
                this.innerHTML = '<small>{{ __("Show more") }}</small>';
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-item.active {
    color: #7d6c7b;
}

.card {
    border-radius: 12px;
    border-color: #7d6c7b;
}

.random-badge {
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-block;
}

/* Стили для баджей уроков */
.keyword-badge {
    padding: 0.35rem 0.65rem;
    border-radius: 0.5rem;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
}

/* Для желтых баджей с темным текстом */
.keyword-badge[style*="color: #000;"] {
    color: #000 !important;
}

.list-group-item {
    padding: 1.25rem;
    border-color: #7d6c7b;
}

.list-group-item:first-child {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.list-group-item:last-child {
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
}

/* Стили для баджей */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.badge.bg-primary {
    background-color: #4681f4 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-dark {
    background-color: #343a40 !important;
}

.btn-sm {
    padding: 0.455rem 0.85rem;
    font-size: 0.875rem;
    border-radius: 8px;
    background-color: #4681f4;
    color: white;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
    display: inline-block;
    text-align: center;
    text-decoration: none;
    line-height: 1.5;
}

/* Состояние hover */
.btn-sm:hover {
    background-color: #5783db;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Состояние active (нажатие) */
.btn-sm:active {
    background-color: #4681f4;
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Состояние focus */
.btn-sm:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(70, 129, 244, 0.3);
}

/* Состояние disabled */
.btn-sm:disabled {
    background-color: #cccccc;
    color: #666666;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Стили для ключевых слов */
.keyword-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.keyword-badge {
    padding: 0.35rem 0.65rem;
    border-radius: 0.5rem;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1;
    white-space: nowrap;
}

/* Стили для переключения описания */
.toggle-description {
    text-decoration: none;
    font-size: 0.8rem;
}

.toggle-description:hover {
    text-decoration: underline;
}

/* Пагинация */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #4681f4;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #4681f4;
    border-color: #4681f4;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }

    .breadcrumb {
        font-size: 0.875rem;
    }

    .h2 {
        font-size: 1.75rem;
    }

    .card-body {
        padding: 1rem;
    }

    .list-group-item {
        padding: 1rem;
    }

    .d-flex.flex-md-row {
        flex-direction: column;
        gap: 1rem;
    }

    .flex-shrink-0 {
        align-self: stretch;
    }

    .btn {
        width: 100%;
    }

    .keyword-container {
        gap: 0.35rem;
    }

    .keyword-badge {
        padding: 0.3rem 0.6rem;
        font-size: 0.7rem;
    }
}

@media (max-width: 576px) {
    .h2 {
        font-size: 1.5rem;
    }

    .card-header h5 {
        font-size: 1rem;
    }

    .card-body {
        padding: 0.75rem;
    }

    .list-group-item {
        padding: 0.75rem;
    }

    .progress {
        height: 18px !important;
    }

    .keyword-badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.65rem;
    }
}
</style>
@endpush
