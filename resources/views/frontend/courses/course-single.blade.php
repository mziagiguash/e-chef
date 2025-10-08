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
    {{-- Бейдж доступа в виде флажка --}}
{{-- Альтернативный современный стиль --}}
@if($hasAccess)
<div class="access-flag-modern access-flag-modern--success mt-3">
    <i class="fa-solid fa-universal-access"></i>
    {{ __('You have full access to this course') }}
</div>
@else
<div class="access-flag-modern access-flag-modern--warning mt-3">
    <i class="fa-solid fa-user-slash"></i>
    {{ __('Enroll to get full access') }}
</div>
@endif
</div>

            <div class="row g-4">
                <div class="col-lg-8">
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

                    {{-- РАЗДЕЛ УРОКОВ - ТОЛЬКО ДЛЯ ПОЛЬЗОВАТЕЛЕЙ С ДОСТУПОМ --}}
                    @if($student)
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

                                        $materialsCount = $lesson->materials ? $lesson->materials->count() : 0;
                                        $hasQuiz = $lesson->quiz ? true : false;
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
                                                        <span class="keyword-badge" style="background-color: #4681f4">
                                                            <i class="fas fa-video me-1"></i> {{ __('Video') }}
                                                        </span>
                                                    @endif

                                                    @if($hasQuiz)
                                                        <span class="keyword-badge" style="background-color: #ffc107; color: #000;">
                                                            <i class="fas fa-graduation-cap me-1"></i> {{ __('Quiz') }}
                                                        </span>
                                                    @endif

                                                    @if($materialsCount > 0)
                                                        <span class="keyword-badge" style="background-color: #28a745">
                                                            <i class="fas fa-file-download me-1"></i> {{ __('Materials') }} ({{ $materialsCount }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex-shrink-0">
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
                        </div>
                    </div>
                    @else
                    {{-- РАЗДЕЛ ДЛЯ ПОЛЬЗОВАТЕЛЕЙ БЕЗ ДОСТУПА --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                {{ __('Course Overview') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-book fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-1">{{ $totalLessons }}</h5>
                                        <small class="text-muted">{{ __('Lessons') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-1">{{ $course->duration ?? 0 }}</h5>
                                        <small class="text-muted">{{ __('Hours') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-1">{{ $course->level ?? 'All' }}</h5>
                                        <small class="text-muted">{{ __('Level') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Призыв к действию --}}
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body text-center py-5">
                            <h4 class="mb-3">{{ __('Ready to Start Learning?') }}</h4>
                            <p class="mb-4">{{ __('Enroll now to get full access to all lessons, quizzes, and course materials.') }}</p>
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <a href="{{ route('add.to.cart', ['locale' => $locale, 'id' => $course->id]) }}"
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    {{ __('Add to Cart') }}
                                </a>
                                <a href="{{ route('checkout', ['locale' => $locale]) }}?course_id={{ $course->id }}"
                                   class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-bolt me-2"></i>
                                    {{ __('Buy Now') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    {{-- ПРОГРЕСС И СЕРТИФИКАТ - ТОЛЬКО ДЛЯ ПОЛЬЗОВАТЕЛЕЙ С ДОСТУПОМ --}}
                    @if($student)
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="mb-3">{{ __('Course Progress') }}</h6>

                            {{-- Круговой прогресс-бар --}}
                            <div class="position-relative d-inline-block mb-3">
                                <div class="progress-circle" data-progress="{{ $progress }}">
                                    <svg width="120" height="120" viewBox="0 0 120 120">
                                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                        <circle cx="60" cy="60" r="54" fill="none" stroke="#28a745" stroke-width="8"
                                                stroke-dasharray="339.292" stroke-dashoffset="{{ 339.292 * (1 - $progress / 100) }}"
                                                stroke-linecap="round" transform="rotate(-90 60 60)"/>
                                    </svg>
                                    <div class="progress-text">
                                        <span class="h4 mb-0 fw-bold">{{ $progress }}%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="progress mb-2" style="height: 12px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                     style="width: {{ $progress }}%">
                                </div>
                            </div>
                            <small class="text-muted d-block mb-3">
                                {{ $completedLessons }}/{{ $totalLessons }} {{ __('lessons completed') }}
                            </small>

                            {{-- Кнопка сертификата --}}
                            @if($canGenerateCertificate)
                            <div class="certificate-section mt-4 p-3 bg-light rounded">
                                <i class="fas fa-award fa-2x text-warning mb-2"></i>
                                <h6 class="mb-2">{{ __('Course Completed!') }}</h6>
                                <p class="small text-muted mb-3">{{ __('Congratulations! You have successfully completed this course.') }}</p>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#certificateModal">
                                    <i class="fas fa-download me-1"></i> {{ __('Download Certificate') }}
                                </button>
                            </div>
                            @else
                            <div class="certificate-info mt-3 p-3 bg-light rounded">
                                <i class="fas fa-trophy text-muted mb-2"></i>
                                <p class="small text-muted mb-0">
                                    {{ __('Complete all lessons to get your certificate of completion') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- БЛОК С ЦЕНОЙ И ОФОРМЛЕНИЕМ --}}
                    <div class="courseCard--wrapper mb-4">
                        <div class="cart border-0 shadow-sm">
                            <div class="cart__price p-3 bg-light rounded-top">
                                <div class="current-price d-flex justify-content-between align-items-center">
                                    <h3 class="font-title--sm mb-0">
                                        @if($course->price > 0)
                                            {{ $currencySymbol }}{{ number_format($course->price * $currencyRate, 2) }}
                                        @else
                                            {{ __('Free') }}
                                        @endif
                                    </h3>
                                    @if($course->old_price > 0 && $course->old_price > $course->price)
                                        <div class="text-end">
                                            <p class="mb-0"><del class="text-muted">{{ $currencySymbol }}{{ number_format($course->old_price * $currencyRate, 2) }}</del></p>
                                            @php
                                                $discount = (($course->old_price - $course->price) / $course->old_price) * 100;
                                            @endphp
                                            <div class="current-discount">
                                                <p class="font-para--md mb-0 text-success">{{ round($discount) }}% {{ __('off') }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="cart__checkout-process p-3">
                                @if(!$hasAccess)
                                    {{-- Кнопки покупки для пользователей без доступа --}}
                                    @if($course->old_price > 0 && $course->old_price > $course->price)
                                        <p class="time-left text-center text-muted mb-3">
                                            <i class="fas fa-clock me-1"></i>
                                            <span>{{ __('5 hours to remaining this price') }}</span>
                                        </p>
                                    @endif

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('add.to.cart', ['locale' => $locale, 'id' => $course->id]) }}"
                                           class="text-white button button-lg button--primary w-100 btn btn-primary">
                                            {{ __('Add to Cart') }}
                                        </a>
                                        <a href="{{ route('checkout', ['locale' => $locale]) }}?course_id={{ $course->id }}"
                                           class="button button-lg button--primary-outline mt-0 w-100 btn btn-outline-primary">
                                            {{ __('Buy Now') }}
                                        </a>
                                    </div>
                                @else
                                    {{-- Информация о доступе для пользователей с доступом --}}
                                    <div class="alert alert-success mb-0 text-center">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ __('You have full access to this course') }}
                                        <br>
                                        <small class="mt-1 d-block">
                                            <a href="{{ route('lessons.show', ['locale' => $locale, 'course' => $course->id, 'lesson' => $course->lessons->first()->id ?? 0]) }}"
                                               class="btn btn-primary btn-sm mt-2">
                                                <i class="fas fa-play me-1"></i> {{ __('Start Learning') }}
                                            </a>
                                        </small>
                                    </div>
                                @endif
                            </div>

                            {{-- Остальные блоки (включения, шеринг) --}}
                            <div class="cart__includes-info p-3 border-top">
                                <h6 class="font-title--card mb-3">{{ __('This course includes:') }}</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex align-items-center mb-2">
                                        <span class="me-2"><i class="fas fa-infinity text-primary"></i></span>
                                        <p class="font-para--md mb-0">{{ __('Full Lifetime Access') }}</p>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <span class="me-2"><i class="fas fa-undo text-primary"></i></span>
                                        <p class="font-para--md mb-0">{{ __('30 Days Money Back Guarantee') }}</p>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <span class="me-2"><i class="fas fa-file-download text-primary"></i></span>
                                        <p class="font-para--md mb-0">{{ __('Free Exercises File') }}</p>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <span class="me-2"><i class="fas fa-mobile-alt text-primary"></i></span>
                                        <p class="font-para--md mb-0">{{ __('Access on Mobile, Tablet and TV') }}</p>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <span class="me-2"><i class="fas fa-certificate text-primary"></i></span>
                                        <p class="font-para--md mb-0">{{ __('Certificate of Completion') }}</p>
                                    </li>
                                </ul>
                            </div>

                            <div class="cart__share-content p-3 border-top">
                                <h6 class="font-title--card mb-3">{{ __('Share This Course') }}</h6>
                                <ul class="social-icons social-icons--outline list-unstyled d-flex gap-2 mb-0">
                                    <li>
                                        <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-dark" style="width: 36px; height: 36px;">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-dark" style="width: 36px; height: 36px;">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-dark" style="width: 36px; height: 36px;">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-dark" style="width: 36px; height: 36px;">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-dark" style="width: 36px; height: 36px;">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- ИНФОРМАЦИЯ О КУРСЕ --}}
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
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Модальное окно сертификата --}}
@if($hasAccess && $canGenerateCertificate)
@include('frontend.courses.partials.certificate-modal')
@endif

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

    // Анимация кругового прогресса
    const progressCircle = document.querySelector('.progress-circle');
    if (progressCircle) {
        const progress = progressCircle.getAttribute('data-progress');
        const circle = progressCircle.querySelector('circle:last-child');
        const radius = circle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;

        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference - (progress / 100) * circumference;
    }

    // Загрузка сертификата - ОДИН обработчик
    const downloadButtons = document.querySelectorAll('.download-certificate');
    downloadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const format = this.getAttribute('data-format');
            generateCertificate(format, e);
        });
    });

    function generateCertificate(format, event) {
    const courseId = {{ $course->id }};
    const studentId = {{ $studentId ?? 'null' }};

    if (!studentId) {
        alert('{{ __("Student not found") }}');
        return;
    }

    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __("Generating") }}';
    button.disabled = true;

    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('format', format);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`/{{ $locale }}/courses/${courseId}/certificate/generate`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.download_url && data.download_url.startsWith('data:')) {
                const link = document.createElement('a');
                link.href = data.download_url;
                link.download = data.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Закрываем модальное окно
                const modal = bootstrap.Modal.getInstance(document.getElementById('certificateModal'));
                if (modal) modal.hide();

                showAlert('{{ __("Certificate downloaded successfully!") }}', 'success');
            } else {
                alert('{{ __("Download URL not available") }}');
            }
        } else {
            // Информативное сообщение об ошибке
            if (data.message.includes('not available')) {
                showAlert('{{ __("PNG/JPG certificates are not available. Please use PDF format.") }}', 'warning');
            } else {
                alert(data.message || '{{ __("Error generating certificate") }}');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("Network error. Please try again.") }}');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

    // Функция для показа уведомлений
    function showAlert(message, type = 'info') {
        // Создаем контейнер для алертов если его нет
        let alertContainer = document.querySelector('.alert-container');
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.className = 'alert-container position-fixed top-0 start-50 translate-middle-x mt-3';
            alertContainer.style.zIndex = '1060';
            document.body.appendChild(alertContainer);
        }

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        alertContainer.appendChild(alertDiv);

        // Автоматически скрываем через 5 секунд
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endpush

{{-- Добавьте стили из watch-course.blade.php --}}

@push('styles')
<style>
.lesson-locked {
    opacity: 0.6;
    pointer-events: none;
}

.lesson-locked .badge {
    font-size: 0.7em;
}

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

/* Альтернативный современный стиль для флажков */
.access-flag-modern {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.access-flag-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.access-flag-modern:hover::before {
    left: 100%;
}

.access-flag-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.access-flag-modern--success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: 2px solid #28a745;
}

.access-flag-modern--warning {
    background: linear-gradient(135deg, #b58a07, #ffb300);
    color: #000;
    border: 2px solid #e0a800;
}

.access-flag-modern i {
    font-size: 1.1em;
    margin-right: 8px;
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

/* Стили для блока цены */
.courseCard--wrapper {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.cart__price {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.cart__checkout-process .btn {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-weight: 600;
}

.cart__includes-info li {
    padding: 0.25rem 0;
}

.social-icons a {
    transition: all 0.3s ease;
}

.social-icons a:hover {
    transform: translateY(-2px);
    background: #4681f4 !important;
    color: white !important;
}

/* Стили для прогресса и сертификата */
.progress-circle {
    position: relative;
    display: inline-block;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.certificate-section {
    border-left: 4px solid #28a745;
}

.certificate-info {
    border-left: 4px solid #6c757d;
}

.certificate-design {
    background: linear-gradient(135deg, #fffaf0 0%, #fff 100%);
    font-family: 'Georgia', serif;
}

.certificate-border {
    border-style: double !important;
    border-width: 15px !important;
}

/* Анимация прогресса */
.progress-bar-animated {
    animation: progress-animation 1.5s ease-in-out infinite;
}

@keyframes progress-animation {
    0% { background-position: 0 0; }
    100% { background-position: 40px 0; }
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

    .progress-circle svg {
        width: 100px;
        height: 100px;
    }

    .progress-text .h4 {
        font-size: 1.25rem;
    }

    .certificate-preview {
        padding: 1rem !important;
    }

    .certificate-border {
        border-width: 8px !important;
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

    .social-icons {
        justify-content: center;
    }
}
</style>
@endpush
